<template></template>
<script setup>
import { Auth } from "@/services/Auth.js";
import { onMounted } from "vue";

onMounted(() => {
    Auth.logout();
})
</script>
