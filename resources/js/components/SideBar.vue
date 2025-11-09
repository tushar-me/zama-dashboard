<script setup lang="ts">
import { ref } from "vue";
import { Link, router } from '@inertiajs/vue3';
import { motion } from "motion-v"

const openIndex = ref<number | null>(null);

const toggle = (index: number) => {
  openIndex.value = openIndex.value === index ? null : index;
};

const handleLogout = () => {
  router.flushAll();
};

const items = ref([
  {
    label: 'Dashboard',
    icon: 'tabler:home',
    to: '/dashboard',
  },
  {
    label: 'Vendors',
    icon: 'material-symbols-light:person',
    items: [
      { label: 'Vendors', icon: 'material-symbols-light:person', to: '/vendor' },
      { label: 'Stores', icon: 'material-symbols-light:store', to: '/store' },
      { label: 'Campaigns', icon: 'material-symbols-light:campaign', to: '/campaign' },
      { label: 'Products', icon: 'icon-park-outline:ad-product', to: '/product' },
      { label: 'Badge', icon: 'mdi:police-badge-outline', to: '/badge' },
    ],
  },
  {
    label: 'Mockups',
    icon: 'hugeicons:shirt-01',
    items: [
      { label: 'Create Mockup', icon: 'material-symbols:playlist-add', to: '/mockup/create' },
      { label: 'All Mockups', icon: 'material-symbols-light:list', to: '/mockup' },
      { label: 'Mockup Side', icon: 'material-symbols-light:view-sidebar', to: '/mockup-side'},
      { label: 'Category', icon: 'material-symbols:event-list-outline-rounded', to: '/category' }
    ],
  },
  {
    label: 'Variation',
    icon: 'streamline:interface-page-controller-settings-page-setting-square-triangle-circle-line-combination-variation',
    items: [
      { label: 'Colors', icon: 'proicons:color-palette', to: '/color' },
      { label: 'Sizes', icon: 'hugeicons:resize-field-rectangle', to: '/size' },
    ],
  },
  {
    label: 'Logistics',
    icon: 'mdi:airplane-marker',
    items: [
      { label: 'Country', icon: 'material-symbols:globe-location-pin', to: '/country' },
      { label: 'State', icon: 'hugeicons:real-estate-02', to: '/state' },
      { label: 'City', icon: 'ph:city', to: '/city' },
      { label: 'Others', icon: 'material-symbols:event-list-outline-rounded', to: '/category' },
    ],
  },
  {
    label: 'Order',
    icon: 'tabler:shopping-bag',
    to: '/order',
  },
  {
    label: 'OrderStatus',
    icon: 'tabler:shopping-bag',
    to: '/order-status',
  },
  {
    label: 'Transaction',
    icon: 'tdesign:undertake-transaction',
    to: '/transaction',
  },
  {
    label: 'Accounts',
    icon: 'streamline-plump:user-multiple-accounts',
    items: [
      { label: 'Payment Methods', icon: 'fluent:payment-24-regular', to: '/payment-method' },
    ],
  },
  {
    label: 'Report',
    icon: 'ix:report-linechart',
    to: '/report',
  },
]);
</script>

<template>
  <ul 
    class="w-56 fixed top-0 bottom-0 -left-64 lg:left-1 p-2 pe-3 z-20 overflow-y-auto space-y-1">
    <li v-for="(item, index) in items" :key="index" class="relative">
      <!-- Menu header -->
      <div
        class="flex items-center justify-between px-2 ps-4 py-2 text-gray-900 hover:bg-white hover:shadow cursor-pointer rounded-xl transition"
        @click="item.items ? toggle(index) : router.visit(item.to!)"
      >
        <div class="flex items-center gap-2">
          <Icon :name="item.icon" class="text-lg" />
          <span class="font-normal text-base">{{ item.label }}</span>
        </div>
      </div>

      <!-- Smooth Collapsible Submenu -->
      <motion.div
        v-if="item.items"
        v-motion
        :initial="{ height: 0, opacity: 0 }"
        :animate="openIndex === index ? { height: 'auto', opacity: 1 } : { height: 0, opacity: 0 }"
        :transition="{ type: 'tween', duration: 0.3 }"
        class="overflow-hidden"
      >
        <div class="space-y-1 p-2 bg-stone-200 rounded-2xl shadow mt-2">
          <Link
            v-for="(sub, i) in item.items"
            :key="i"
            :href="sub.to"
            class="flex items-center gap-2 px-2 ps-4 py-2 text-gray-900 hover:bg-white hover:shadow cursor-pointer rounded-xl transition"
          >
            <Icon :name="sub.icon" class="text-base" />
            <span>{{ sub.label }}</span>
          </Link>
        </div>
      </motion.div>
    </li>

    <!-- Logout -->
    <Link
      method="post"
      :href="route('logout')"
      @click="handleLogout"
      as="button"
      class="w-full flex items-center px-4 py-3 mt-4 text-stone-100 hover:bg-neutral-800 cursor-pointer rounded-lg gap-3"
    >
      <Icon name="ri:logout-circle-line" class="text-xl" />
      <span class="font-normal text-lg">Logout</span>
    </Link>
  </ul>
</template>
