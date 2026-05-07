<template>
    <svg>
        <defs>
            <!--God knows how this svg is constructed-->
            <marker :id="markerId" viewBox="0 0 20 20" refX="8" refY="5"
                    markerWidth="70" markerHeight="140" orient="auto-start-reverse">
                <path d="M0,0 L5,5 M5,5 L5,5 M5,5 L10,0 M5,5 L10,0" fill="none" :stroke="strokeColor"
                      transform="rotate(90 5 5)" stroke-width="0.15" />
            </marker>
        </defs>
    </svg>

    <BaseEdge :id="id" :style="edgeStyle" :path="edgePath"
              :marker-start="resolveMarker(data?.markerStart)" :marker-end="resolveMarker(data?.markerEnd)" />
</template>

<script setup>
import { BaseEdge } from '@vue-flow/core'
import { computed } from 'vue'
import { useEdgeRouting } from '@/composables/useEdgeRouting.js'

defineOptions({ inheritAttrs: false })

const props = defineProps({
    id: String,
    source: String,
    target: String,
    sourceX: Number,
    sourceY: Number,
    targetX: Number,
    targetY: Number,
    sourcePosition: String,
    targetPosition: String,
    data: Object,
    style: Object,
})

const { routeEdge } = useEdgeRouting()

const edgePath = computed(() => routeEdge(props))
const markerId = computed(() => `chickenFoot-${props.id}`)
const strokeColor = computed(() => props.style?.stroke || 'var(--color-primary)')
const edgeStyle = computed(() => ({ fill: 'none', ...props.style }))
const resolveMarker = (val) => val === 'url(#chickenFoot)' ? `url(#${markerId.value})` : (val || 'none')
</script>
