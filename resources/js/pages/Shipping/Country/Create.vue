<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import UFileUpload from '@/components/UFileUpload.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { ref, defineOptions, defineProps } from 'vue';

defineOptions({ layout: AppLayout });

const { errors } = defineProps<{
    errors: Record<string, string | undefined>;
}>();

const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Country' },
    { label: 'Create' }
]);

const toast = useToast();

const form = useForm({
    name: '',
    flag: null as File | null,
    phone_code: '',
    iso2: '',
    iso3: '',
    status: true,
    currency: '',
    currency_symbol: '',
    shipping_charge: 0,
    tax_percentage: 0,
    vat_percentage: 0,
    order_level: 0,
});

const onSubmit = () => {
    form.post(route('country.store'), {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Country has been created successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            router.visit(route('country.index'));
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        },
    });
};
</script>  
<template>
    <Head title="Create Country" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <Breadcrumb :home="home" :model="items" />
        <div class="p-10 pt-5">
            <form @submit.prevent="onSubmit">
            <div class="flex gap-4 mb-4">
                <label for="name" class="font-normal lg:w-1/4">Name <span class="text-red-500">*</span></label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.name" id="name" :invalid="!!errors.name" autocomplete="off" />
                    <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="price"  class="font-normal lg:w-1/4">Phone Code <span class="text-red-500">*</span></label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.phone_code" id="phone_code" :invalid="!!errors.phone_code" autocomplete="off" />
                    <Message v-if="errors.phone_code" severity="error" size="small" variant="simple">{{ errors.phone_code }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="iso2" class="font-normal lg:w-1/4">ISO 2</label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.iso2" id="iso2" :invalid="!!errors.iso2" autocomplete="off" />
                    <Message v-if="errors.iso2" severity="error" size="small" variant="simple">{{ errors.iso2 }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="iso3" class="font-normal lg:w-1/4">ISO 3</label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.iso3" id="iso3" :invalid="!!errors.iso3" autocomplete="off" />
                    <Message v-if="errors.iso3" severity="error" size="small" variant="simple">{{ errors.iso3 }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="currency" class="font-normal lg:w-1/4">Currency</label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.currency" id="currency" :invalid="!!errors.currency" autocomplete="off" />
                    <Message v-if="errors.currency" severity="error" size="small" variant="simple">{{ errors.currency }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="currency_symbol" class="font-normal lg:w-1/4">Currency Symbol</label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.currency_symbol" id="currency_symbol" :invalid="!!errors.currency_symbol" autocomplete="off" />
                    <Message v-if="errors.currency_symbol" severity="error" size="small" variant="simple">{{ errors.currency_symbol }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="shipping_charge" class="font-normal lg:w-1/4">Shipping Charge</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber v-model="form.shipping_charge" id="shipping_charge" :invalid="!!errors.shipping_charge" autocomplete="off" />
                    <Message v-if="errors.shipping_charge" severity="error" size="small" variant="simple">{{ errors.shipping_charge }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="tax_percentage" class="font-normal lg:w-1/4">Tax Percentage</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber v-model="form.tax_percentage" id="tax_percentage" :invalid="!!errors.tax_percentage" autocomplete="off" />
                    <Message v-if="errors.tax_percentage" severity="error" size="small" variant="simple">{{ errors.tax_percentage }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="vat_percentage" class="font-normal lg:w-1/4">VAT Percentage</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber v-model="form.vat_percentage" id="vat_percentage" :invalid="!!errors.vat_percentage" autocomplete="off" />
                    <Message v-if="errors.vat_percentage" severity="error" size="small" variant="simple">{{ errors.vat_percentage }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-2">
                <label for="flag" class="font-normal lg:w-1/4">Flag <span class="text-red-500">*</span></label>
                   <div class="w-full lg:w-3/4 max-w-96">
                    <!-- 
                        On an edit page, you would pass the existing image URL like this:
                        <UFileUpload v-model="form.flag" accept="image/*" :multiple="false" :initialImage="props.country?.flag_url" />
                    -->
                    <UFileUpload v-model="form.flag" accept="image/*" :multiple="false" />
                        <span v-if="errors.flag" class="text-red-500">{{ errors.flag }}</span>
                   </div>
                </div>
        
            <div class="flex gap-4 mt-4">
                <div class="w-full lg:w-1/4">
                 </div>
                <Button class="w-80" type="submit" label="Save" icon="pi pi-save"  block />
            </div>
           
            </form>
        </div>
    </div>
</template>
