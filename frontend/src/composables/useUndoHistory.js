const MAX_HISTORY = 50

export function useUndoHistory(schema) {
    const history = []
    const redoStack = []
    let lastSessionKey = null

    const snapshot = (sessionKey = null) => {
        if (!schema.value) return
        if (sessionKey && sessionKey === lastSessionKey) return
        history.push(JSON.parse(JSON.stringify(schema.value)))
        if (history.length > MAX_HISTORY) history.shift()
        redoStack.length = 0
        lastSessionKey = sessionKey
    }

    const undo = () => {
        if (!history.length) return null
        redoStack.push(JSON.parse(JSON.stringify(schema.value)))
        lastSessionKey = null
        return history.pop()
    }

    const redo = () => {
        if (!redoStack.length) return null
        history.push(JSON.parse(JSON.stringify(schema.value)))
        lastSessionKey = null
        return redoStack.pop()
    }

    return { snapshot, undo, redo }
}
