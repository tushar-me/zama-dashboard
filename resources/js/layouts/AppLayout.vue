<script setup lang="ts">
    import SideBar from '@/components/SideBar.vue';
    import TopBar from '@/components/TopBar.vue';
</script>
<template>
    <main class="bg-neutral-100 min-h-screen">
        <TopBar />
        <SideBar />
        <div class="lg:ml-64 lg:pt-18 p-2 lg:p-4">
            <slot />
        </div>
    </main>
</template>