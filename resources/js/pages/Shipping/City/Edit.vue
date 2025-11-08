<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { ref, watch } from 'vue';
import type { Country, City } from '@/types';
defineOptions({ layout: AppLayout });

const props = defineProps<{
    errors: any,
    countries: Country[],
    city: City
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'City' },
    { label: 'Edit' }
]);

const selectedCountry = ref<Country | null>(props.city.country ?? null);
const toast = useToast();

const form = useForm({
    _method: 'put',
    name: props.city.name ?? '',
    country_id: props.city.country_id ?? '',
    state_id: props.city.state_id ?? '',
    shipping_charge: props.city.tax_percentage ?? '',
    tax_percentage: props.city.tax_percentage ?? '',
    vat_percentage: props.city.vat_percentage ?? '',
    is_default: false,
    order_level: props.city.order_level ?? 0,
    status: true,
})

const onSubmit = () => {
    form.post(route('city.update', props.city.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'City has been created successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        },
    });
}

watch(() => selectedCountry.value, (newValue: Country) => {
    if (newValue) {
        form.country_id = newValue.id;
    } else {
        form.country_id = '';
    }
});
</script>  
<template>
    <Head title="Edit City" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <Breadcrumb :home="home" :model="items" />
        <div class="p-10 pt-5">
            <form @submit.prevent="onSubmit">
            <div class="flex gap-4 mb-4">
                <label for="country_id" class="font-normal w-1/4">Country <span class="text-red-500">*</span></label>
                <div class="w-full lg:w-3/4">
                    <Select class="w-64" v-model="selectedCountry" :options="countries" filter  optionLabel="name" placeholder="Select a Country" />
                    <Message v-if="errors.country_id" severity="error" size="small" variant="simple">{{ errors.country_id }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="country_id" class="font-normal w-1/4">State <span class="text-red-500">*</span></label>
                <div class="w-full lg:w-3/4">
                    <Select class="w-64" v-model="form.state_id" :options="selectedCountry?.states" filter optionValue="id" optionLabel="name" placeholder="Select a State" />
                    <Message v-if="errors.state_id" severity="error" size="small" variant="simple">{{ errors.state_id }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="name"  class="font-normal w-1/4">Name  <span class="text-red-500">*</span></label>
                <div class="w-full lg:w-3/4">
                    <InputText class="w-64" v-model="form.name" id="name" :invalid="errors.name" autocomplete="off" />
                    <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                </div>
            </div>

            <div class="flex gap-4 mb-4">
                <label for="shipping_charge" class="font-normal w-1/4">Shipping Charge</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber class="w-64" currency="USD" mode="currency" v-model="form.shipping_charge" id="shipping_charge" :invalid="errors.shipping_charge" autocomplete="off" />
                    <Message v-if="errors.shipping_charge" severity="error" size="small" variant="simple">{{ errors.shipping_charge }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="tax_percentage" class="font-normal w-1/4">Tax Percentage</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber class="w-64" v-model="form.tax_percentage" id="tax_percentage" :invalid="errors.tax_percentage" autocomplete="off" />
                    <Message v-if="errors.tax_percentage" severity="error" size="small" variant="simple">{{ errors.tax_percentage }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="vat_percentage" class="font-normal w-1/4">VAT Percentage</label>
                 <div class="w-full lg:w-3/4">
                    <InputNumber class="w-64" v-model="form.vat_percentage" id="vat_percentage" :invalid="errors.vat_percentage" autocomplete="off" />
                    <Message v-if="errors.vat_percentage" severity="error" size="small" variant="simple">{{ errors.vat_percentage }}</Message>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="w-1/4"></div>
                <div class="w-full lg:w-3/4">
                    <Button type="submit" label="Save" icon="pi pi-save" class="w-64" block />
                </div>
            </div>
           
            </form>
        </div>
    </div>
</template>
