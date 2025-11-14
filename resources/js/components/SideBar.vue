<script setup lang="ts">
import { ref,computed } from "vue";
import { Link, router, usePage } from '@inertiajs/vue3';
import { motion } from "motion-v"

const page = usePage()
const user = computed(() => page.props.auth.user)

const openIndex = ref<number | null>(null);

const toggle = (index: number) => {
  openIndex.value = openIndex.value === index ? null : index;
};
const items = ref([
  {
    label: 'Dashboard',
    icon: 'hugeicons:dashboard-circle-settings',
    to: '/dashboard',
  },
  {
    label: 'Collections',
    icon: 'hugeicons:collections-bookmark',
    to: '/collection'
  },
  {
    label: 'Products',
    icon: 'solar:box-outline',
    items: [
      { label: 'Create Products', icon: 'solar:inbox-in-bold', to: '/product/create' },
      { label: 'All Products', icon: 'lucide:boxes', to: '/product' },
      { label: 'Category', icon: 'material-symbols:event-list-outline-rounded', to: '/category' }
    ],
  },
  {
    label: 'Variation',
    icon: 'mdi:newspaper-variant-outline',
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
    ],
  },
  {
    label: 'Order',
    icon: 'hugeicons:shopping-basket-01',
    items: [
      { label: 'Orders', icon: 'hugeicons:shopping-basket-done-01', to: '/order' },
      { label: 'Order Status', icon: 'carbon:status-change', to: '/order-status' },
      { label: 'Return Order', icon: 'hugeicons:shopping-basket-remove-01', to: '/city' },
      { label: 'Cancelled Order', icon: 'hugeicons:shopping-basket-remove-01', to: '/city' },
    ],
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
      { label: 'Payment Accounts', icon: 'hugeicons:pay-by-check', to: '/payment-method' },
    ],
  },
  {
    label: 'Report',
    icon: 'ix:report-linechart',
    to: '/report',
  },
]);

const menu = ref();
const menuitems = ref([
    {
        label: 'Profile',
        items: [
            {
                label: 'Settings',
                icon: 'pi pi-cog',
                shortcut: '⌘+O'
            },
            {
                label: 'Logout',
                icon: 'pi pi-sign-out',
                shortcut: '⌘+Q',
                command: () => {
                    router.post(route('logout'));
                }
            },
        ]
    }
]);

const menuToggle = (event: any) => {
    menu.value.toggle(event);
};
</script>

<template>
  <ul 
    class="w-60 fixed top-0 bottom-0 -left-64 lg:left-1 p-2 pe-3  overflow-y-auto space-y-1">
    <li>
      <div>
          <div type="button" class="flex items-center justify-between p-2 cursor-pointer hover:bg-white hover:shadow rounded-lg"  @click="menuToggle">
           <div>
              <Avatar icon="pi pi-user" style="background-color: #dee9fc; color: #1a2551" />
              {{ user?.name }}
           </div>
            <Icon name="material-symbols:expand-all" />
          </div>
          <Menu ref="menu" id="overlay_menu" :model="menuitems" :popup="true" />
      </div>
    </li>
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
  </ul>
</template>
