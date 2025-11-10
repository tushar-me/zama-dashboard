<script setup lang="ts">
    import SideBar from '@/components/SideBar.vue';
    import TopBar from '@/components/TopBar.vue';
</script>
<template>
    <main class="bg-stone-100">
        <TopBar />
        <SideBar />
        <div class="lg:ml-60 p-2 lg:p-4 bg-white shadow-md min-h-screen">
            <slot />
        </div>
    </main>
</template>