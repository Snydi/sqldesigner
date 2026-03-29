<template>
    <div class="remote-cursor" :style="{ transform: `translate(${x}px, ${y}px)` }">
        <svg class="remote-cursor__arrow" width="14" height="18" viewBox="0 0 14 18" xmlns="http://www.w3.org/2000/svg" :fill="color">
            <path d="M0 0 L0 14 L3.5 10.5 L6 17 L8.5 16 L6 9.5 L10.5 9.5 Z"/>
        </svg>
        <span class="remote-cursor__label" :style="{ backgroundColor: color }">{{ name }}</span>
    </div>
</template>

<script setup>
defineProps({
    x: { type: Number, required: true },
    y: { type: Number, required: true },
    name: { type: String, required: true },
    color: { type: String, required: true },
})
</script>

<style scoped>
.remote-cursor {
    position: absolute;
    top: 0;
    left: 0;
    pointer-events: none;
    display: flex;
    align-items: flex-start;
    z-index: 9999;
    transition: transform 0.08s linear;
}

.remote-cursor__arrow {
    filter: drop-shadow(0 1px 3px rgba(0, 0, 0, 0.35));
    flex-shrink: 0;
}

.remote-cursor__label {
    font-size: 11px;
    font-weight: 600;
    color: white;
    padding: 2px 7px;
    border-radius: 3px;
    white-space: nowrap;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    margin-left: 2px;
    margin-top: 12px;
    line-height: 18px;
}
</style>
