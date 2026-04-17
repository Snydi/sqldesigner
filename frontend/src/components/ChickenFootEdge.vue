<template>
    <svg>
        <defs>
            <!--God knows how this svg is constructed-->
            <marker id="chickenFoot" viewBox="0 0 20 20" refX="8" refY="5"
                    markerWidth="70" markerHeight="140" orient="auto-start-reverse">
                <path d="M0,0 L5,5 M5,5 L5,5 M5,5 L10,0 M5,5 L10,0" fill="none" stroke="context-stroke"
                      transform="rotate(90 5 5)" stroke-width="0.15" />
            </marker>
        </defs>
    </svg>

    <BaseEdge :id="id" :style="style" :path="edgePath"
              :marker-start="data?.markerStart || 'none'" :marker-end="data?.markerEnd || 'none'" />
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
</script>
