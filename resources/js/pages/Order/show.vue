<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed,ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
  order: {
    type: Object,
    required: true
  },
  statuses: Object
});

// Helper to format the date
const formattedCreatedAt = computed(() => {
  if (props.order?.data?.created_at) {
    return new Date(props.order.data.created_at).toLocaleString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    });
  }
  return 'Date not available';
});


const allOrderDetails = computed(() => {
  if (!props.order?.data?.orders) {
    return [];
  }
  return props.order.data.orders.flatMap(storeOrder => storeOrder.order_details);
});

const activeIndex = ref(0);
const responsiveOptions = ref([
    {
        breakpoint: '1024px',
        numVisible: 5
    },
    {
        breakpoint: '768px',
        numVisible: 3
    },
    {
        breakpoint: '560px',
        numVisible: 1
    }
]);
const displayCustom = ref(false);

const imageClick = (index: number) => {
    activeIndex.value = index;
    displayCustom.value = true;
};
const statusState = useForm({
  status: null,
  message: null
});
const onStatusChange = () => {

}
</script>

<template>
  <Head :title="'Order - ' + order?.data?.order_code" />

  <div>
    <div class="flex justify-between">
      <div>
        <h1 class="text-xl text-gray-800">
          Order #{{ order.data.order_code }}
        </h1>
        <p class="text-sm text-gray-600">
          {{ formattedCreatedAt }}
        </p>
      </div>
      <div>
        <p class="text-sm text-gray-600">Order Status</p>
        <Select v-model="statusState.status" @change="onStatusChange" :options="statuses" optionLabel="name" optionValue="id" placeholder="Order Status" class="w-full md:w-56" />
      </div>
    </div>

    <div class="mt-2 flex flex-col xl:flex-row jusitfy-center items-stretch w-full xl:space-x-8 space-y-4 md:space-y-6 xl:space-y-0">
      <div class="flex flex-col justify-start items-start w-full space-y-4 md:space-y-6 xl:space-y-8">
        <div class="flex flex-col justify-start items-start dark:bg-gray-800 bg-gray-50 px-4 py-4 md:py-6 md:p-6 xl:p-8 w-full">
          <p class="text-lg md:text-xl dark:text-white font-semibold leading-6 xl:leading-5 text-gray-800">Customer’s Cart</p>
          
          <div v-if="allOrderDetails.length === 0" class="mt-4">
            <p class="text-gray-600 dark:text-gray-300">No items found in this order.</p>
          </div>
          
          <div v-for="(item, index) in allOrderDetails" :key="item.id" 
               class="mt-4 md:mt-6 flex flex-col md:flex-row  w-full"
               :class="{ 'border-t border-gray-200 pt-4': index > 0 }">
            
            <div class="pb-4 md:pb-0 w-full md:w-1/4">
              <Galleria v-model:activeIndex="activeIndex" v-model:visible="displayCustom" :value="item.product_images" :responsiveOptions="responsiveOptions" :numVisible="7"
                  containerStyle="max-width: 850px" :circular="true" :fullScreen="true" :showItemNavigators="true" :showThumbnails="false">
                  <template #item="slotProps">
                      <img :src="slotProps.item" :alt="slotProps.item.alt" style="width: 100%; display: block" />
                  </template>
                  <template #thumbnail="slotProps">
                      <img :src="slotProps.item" :alt="slotProps.item.alt" style="display: block" />
                  </template>
              </Galleria>

              <div v-if="item.product_images" class="grid grid-cols-12 gap-4" style="max-width: 400px">
                  <div v-for="(image, index) of item.product_images" :key="index" class="col-span-4">
                      <img :src="image" :alt="image.alt" style="cursor: pointer" @click="imageClick(index)" />
                  </div>
              </div>
            </div>
            <div class="w-1/4 border-b border-gray-200 md:border-b-0 flex-col flex justify-between items-start pb-8 space-y-4 md:space-y-0">
              <div class="w-full flex flex-col justify-start items-start">
                <h3 class="text-sm font-semibold leading-6 text-gray-800">{{ item.product_name }}</h3>
                <div class="flex justify-start items-start flex-col space-y-2">
                  <p class="text-sm dark:text-white leading-none text-gray-800">
                    <span class="">Size: </span> {{ item.size }}
                  </p>
                  <p class="text-sm dark:text-white leading-none text-gray-800">
                    <span class="">Color: </span> {{ item.color }}
                  </p>
                </div>
              </div>
              <div class="mt-2">
                <p class="text-sm dark:text-white">${{ item.price }}*{{ item.quantity }}</p>
                <p class="text-sm dark:text-white font-semibold leading-6 text-gray-800">Total: ${{ item.total }}</p>
              </div>
            </div>
            <div class="flex items-center w-2/4 bg-gray-100 p-4 rounded-lg gap-2">
              <a :href="artwork" download v-for="artwork in item?.artworks" class="block p-2 border border-gray-400">
                <img class="size-16 object-center mb-2" :src="artwork">
                <button class="bg-primary text-white text-xs px-3 py-1 rounded-lg cursor-pointer">Download</button>
              </a>
            </div>
          </div>
        </div>

        <div class="flex justify-center flex-col md:flex-row items-stretch w-full space-y-4 md:space-y-0 md:space-x-6 xl:space-x-8">
          <div class="flex flex-col justify-center px-4 py-6 md:p-6 xl:p-8 w-full bg-gray-50 dark:bg-gray-800 space-y-6">
            <h3 class="text-xl dark:text-white font-semibold leading-5 text-gray-800">Payment Information</h3>
            <div class="flex justify-between w-full">
              <p class="text-base dark:text-white leading-4 text-gray-800">Payment Status</p>
              <p class="text-base dark:text-gray-300 leading-4 text-gray-600 uppercase">{{ order.data.payment_status }}</p>
            </div>
            <div class="flex justify-between w-full">
              <p class="text-base dark:text-white leading-4 text-gray-800">Payment Method</p>
              <p class="text-base dark:text-gray-300 leading-4 text-gray-600 uppercase">{{ order.data.payment_method }}</p>
            </div>
            <div class="flex justify-between w-full">
              <p class="text-base dark:text-white leading-4 text-gray-800">Paypal Order ID</p>
              <p class="text-base dark:text-gray-300 leading-4 text-gray-600 uppercase">{{ order.data.paypal_order_id }}</p>
            </div>
          </div>
          <div class="flex flex-col px-4 py-6 md:p-6 xl:p-8 w-full bg-gray-50 dark:bg-gray-800 space-y-6">
            <h3 class="text-xl dark:text-white font-semibold leading-5 text-gray-800">Summary</h3>
            <div class="flex justify-center items-center w-full space-y-4 flex-col border-gray-200 border-b pb-4">
              <div class="flex justify-between w-full">
                <p class="text-base dark:text-white leading-4 text-gray-800">Subtotal</p>
                <p class="text-base dark:text-gray-300 leading-4 text-gray-600">${{ order.data.sub_total }}</p>
              </div>
              
              <div class="flex justify-between items-center w-full">
                <p class="text-base dark:text-white leading-4 text-gray-800">Tax ({{ order.data.tax_percentage }}%)</p>
                <p class="text-base dark:text-gray-300 leading-4 text-gray-600">${{ order.data.tax }}</p>
              </div>
              
              <div class="flex justify-between items-center w-full">
                <p class="text-base dark:text-white leading-4 text-gray-800">VAT</p>
                <p class="text-base dark:text-gray-300 leading-4 text-gray-600">${{ order.data.vat }}</p>
              </div>

              <div class="flex justify-between items-center w-full">
                <p class="text-base dark:text-white leading-4 text-gray-800">Shipping</p>
                <p class="text-base dark:text-gray-300 leading-4 text-gray-600">${{ order.data.shipping_charge }}</p>
              </div>
            </div>
            <div class="flex justify-between items-center w-full">
              <p class="text-base dark:text-white font-semibold leading-4 text-gray-800">Total</p>
              <p class="text-base dark:text-gray-300 font-semibold leading-4 text-gray-600">${{ order.data.grand_total }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 dark:bg-gray-800 w-full xl:w-96 flex justify-between items-center md:items-start px-4 py-6 md:p-6 xl:p-8 flex-col">
        <h3 class="text-xl dark:text-white font-semibold leading-5 text-gray-800">Customer</h3>
        <div class="flex flex-col md:flex-row xl:flex-col justify-start items-stretch h-full w-full md:space-x-6 lg:space-x-8 xl:space-x-0">
          <div class="flex flex-col justify-start items-start flex-shrink-0">
            <div class="flex justify-center w-full md:justify-start items-center space-x-4 py-8 border-b border-gray-200">
              <div class="flex justify-start items-start flex-col space-y-2">
                <p class="text-base dark:text-white font-semibold leading-4 text-left text-gray-800">
                  {{ order.data.customer.name }}
                </p>
                </div>
            </div>

            <div class="flex justify-center text-gray-800 dark:text-white md:justify-start items-center space-x-4 py-4 border-b border-gray-200 w-full">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M3 7L12 13L21 7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <p class="cursor-pointer text-sm leading-5 ">{{ order.data.customer.email }}</p>
            </div>

             <div class="flex justify-center text-gray-800 dark:text-white md:justify-start items-center space-x-4 py-4 border-b border-gray-200 w-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6.1-6.1A19.79 19.79 0 0 1 3.08 8.18a2 2 0 0 1 2-2.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
              <p class="cursor-pointer text-sm leading-5 ">{{ order.data.customer.phone }}</p>
            </div>
          </div>
          <div class="flex justify-between xl:h-full items-stretch w-full flex-col mt-6 md:mt-0">
            <div class="flex justify-center md:justify-start xl:flex-col flex-col md:space-x-6 lg:space-x-8 xl:space-x-0 space-y-4 xl:space-y-12 md:space-y-0 md:flex-row items-center md:items-start">
              <div class="flex justify-center md:justify-start items-center md:items-start flex-col space-y-4 xl:mt-8">
                <p class="text-base dark:text-white font-semibold leading-4 text-center md:text-left text-gray-800">Shipping Address</p>
                <p class="w-48 lg:w-full dark:text-gray-300 xl:w-48 text-center md:text-left text-sm leading-5 text-gray-600">
                  {{order.data.customer.country}}, {{order.data.customer.state}}, {{order.data.customer.city}}, {{ order.data.customer.street_address }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>