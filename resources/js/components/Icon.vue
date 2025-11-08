<script setup lang="ts">
import { Icon } from "@iconify/vue";

const props = defineProps<{
     name: string
    }>();
</script>

<template>
    <Icon :icon="name" />
</template>