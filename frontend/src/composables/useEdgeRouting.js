import { getSmoothStepPath, useVueFlow } from '@vue-flow/core'

const GAP = 20
const SAFE_ZONE = 10  // obstacles within this distance of an endpoint table are ignored

function nodeBounds(node, padding = GAP) {
    const pos = node.positionAbsolute ?? node.position
    const w = node.dimensions?.width ?? 200
    const h = node.dimensions?.height ?? 100
    return {
        left: pos.x - padding,
        top: pos.y - padding,
        right: pos.x + w + padding,
        bottom: pos.y + h + padding,
    }
}

function boundsOverlap(a, b) {
    return a.left < b.right && a.right > b.left && a.top < b.bottom && a.bottom > b.top
}

function hitsRect(r, x1, y1, x2, y2) {
    if (x1 === x2) {
        return x1 > r.left && x1 < r.right &&
               Math.min(y1, y2) < r.bottom && Math.max(y1, y2) > r.top
    }
    return y1 > r.top && y1 < r.bottom &&
           Math.min(x1, x2) < r.right && Math.max(x1, x2) > r.left
}

function threeSegmentHits(obstacles, sx, sy, cx, tx, ty) {
    return obstacles.some(o =>
        hitsRect(o, sx, sy, cx, sy) ||
        hitsRect(o, cx, sy, cx, ty) ||
        hitsRect(o, cx, ty, tx, ty)
    )
}

function polylinePath(pts, r = 6) {
    if (pts.length < 2) return ''
    let d = `M ${pts[0].x} ${pts[0].y}`
    for (let i = 1; i < pts.length - 1; i++) {
        const p = pts[i - 1], c = pts[i], n = pts[i + 1]
        const d1x = c.x - p.x, d1y = c.y - p.y
        const d2x = n.x - c.x, d2y = n.y - c.y
        const l1 = Math.hypot(d1x, d1y), l2 = Math.hypot(d2x, d2y)
        if (l1 < 0.001 || l2 < 0.001) {
            d += ` L ${c.x} ${c.y}`
            continue
        }
        const rad = Math.min(r, l1 / 2, l2 / 2)
        const ax = c.x - (d1x / l1) * rad, ay = c.y - (d1y / l1) * rad
        const bx = c.x + (d2x / l2) * rad, by = c.y + (d2y / l2) * rad
        d += ` L ${ax} ${ay} Q ${c.x} ${c.y} ${bx} ${by}`
    }
    const last = pts[pts.length - 1]
    d += ` L ${last.x} ${last.y}`
    return d
}

export function useEdgeRouting() {
    const { getNodes } = useVueFlow()

    function routeEdge(props) {
        const extend = -5
        const sx = props.sourcePosition === 'left' ? props.sourceX - extend
                  : props.sourcePosition === 'right' ? props.sourceX + extend
                  : props.sourceX
        const tx = props.targetPosition === 'left' ? props.targetX - extend
                  : props.targetPosition === 'right' ? props.targetX + extend
                  : props.targetX
        const sy = props.sourceY
        const ty = props.targetY

        const isHorizontal = props.sourcePosition === 'left' || props.sourcePosition === 'right'
        if (!isHorizontal) {
            return getSmoothStepPath({ ...props, sourceX: sx, targetX: tx })[0]
        }

        const srcTableId = getNodes.value.find(n => n.id === props.source)?.parentNode
        const tgtTableId = getNodes.value.find(n => n.id === props.target)?.parentNode

        const srcTableNode = getNodes.value.find(n => n.id === srcTableId)
        const tgtTableNode = getNodes.value.find(n => n.id === tgtTableId)
        const srcSafe = srcTableNode ? nodeBounds(srcTableNode, SAFE_ZONE) : null
        const tgtSafe = tgtTableNode ? nodeBounds(tgtTableNode, SAFE_ZONE) : null

        const obstacles = getNodes.value
            .filter(n => n.type === 'table' && n.id !== srcTableId && n.id !== tgtTableId)
            .map(n => nodeBounds(n))
            .filter(b => !(srcSafe && boundsOverlap(b, srcSafe)) && !(tgtSafe && boundsOverlap(b, tgtSafe)))

        const defaultCX = (sx + tx) / 2

        if (!obstacles.length || !threeSegmentHits(obstacles, sx, sy, defaultCX, tx, ty)) {
            return getSmoothStepPath({ ...props, sourceX: sx, targetX: tx })[0]
        }

        // Try routing the vertical bend just left or right of each obstacle
        const candidates = [
            ...obstacles.map(o => o.left),
            ...obstacles.map(o => o.right),
        ].sort((a, b) => Math.abs(a - defaultCX) - Math.abs(b - defaultCX))

        for (const cx of candidates) {
            if (!threeSegmentHits(obstacles, sx, sy, cx, tx, ty)) {
                return getSmoothStepPath({ ...props, sourceX: sx, targetX: tx, centerX: cx })[0]
            }
        }

        // Last resort: detour above or below all blocking obstacles
        const blocking = obstacles.filter(o => threeSegmentHits([o], sx, sy, defaultCX, tx, ty))
        const topY = Math.min(...blocking.map(o => o.top))
        const botY = Math.max(...blocking.map(o => o.bottom))
        const detourY = Math.abs(sy + ty - 2 * topY) <= Math.abs(sy + ty - 2 * botY) ? topY : botY

        return polylinePath([
            { x: sx, y: sy },
            { x: sx, y: detourY },
            { x: tx, y: detourY },
            { x: tx, y: ty },
        ])
    }

    return { routeEdge }
}
