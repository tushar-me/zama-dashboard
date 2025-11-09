
<template>
    <nav class="fixed hidden top-0 w-full bg-white shadow z-10 p-2 items-center justify-between">
        <!-- <img class="h-10 w-auto" src="@/assets/images/tentomart-dark.png" alt="Tentomart"> -->
        <div class="">
            <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png" @click="toggle" aria-haspopup="true" aria-controls="overlay_menu" shape="circle" />
            <Menu  class="w-full md:w-60" ref="menu" id="overlay_menu" :model="menuItems" :popup="true">
                <template #submenulabel="{ item }">
                    <span class="text-primary font-bold">{{ item.label }}</span>
                </template>
                <template #item="{ item, props }">
                    <a v-ripple class="flex items-center" v-bind="props.action">
                        <span :class="item.icon" />
                        <span>{{ item.label }}</span>
                        <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                        <span v-if="item.shortcut" class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">{{ item.shortcut }}</span>
                    </a>
                </template>
                
                <template #end>
                    <button v-ripple class="relative overflow-hidden w-full border-0 bg-transparent flex items-start p-2 pl-4 hover:bg-surface-100 dark:hover:bg-surface-800 rounded-none cursor-pointer transition-colors duration-200">
                        <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png" class="mr-2" shape="circle" />
                        <span class="inline-flex flex-col items-start">
                            <span class="font-bold">Amy Elsner</span>
                            <span class="text-sm">Admin</span>
                        </span>
                    </button>
                </template>
            </Menu>
        </div>
    </nav>
</template>

<script setup>
import { ref } from "vue";
const menu = ref();
const menuItems = ref([
    {
        label: 'Quick Add',
        items: [
            {
                label: 'New Plan',
                icon: 'pi pi-plus',
            },
            {
                label: 'New Vendor',
                icon: 'pi pi-plus',
            },
            {
                label: 'Search',
                icon: 'pi pi-search',
                
            }
        ]
    },
    {
        label: 'Profile',
        items: [
            {
                label: 'Settings',
                icon: 'pi pi-cog',
            },
            {
                label: 'Messages',
                icon: 'pi pi-inbox',
            },
        ]
    },
    {
        separator: true
    }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};
</script>
