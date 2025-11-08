<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { ref, watch } from 'vue';
import type { Country } from '@/types';


defineOptions({ layout: AppLayout });

// --- Props ---
const props = defineProps<{
    errors: any,
    countries: {
        data: Country[]
    },
}>()

// --- Breadcrumb Configuration ---
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'State' },
    { label: 'Bulk Create' }
]);

// --- Component State ---
const selectedCountry = ref<Country | null>(null);
const toast = useToast();

const form = useForm({
    country_id: '',
    states: [
        {
            name: '',
            shipping_charge: 0,
            tax_percentage: 0,
            vat_percentage: 0,
        }
    ]
});

// --- Helper Functions ---

/**
 * Adds a new, empty city entry to the form.
 */
const addState = () => {
    form.states.push({
        name: '',
        shipping_charge: 0,
        tax_percentage: 0,
        vat_percentage: 0,
    });
};

/**
 * Removes a city entry from the form at a specific index.
 * @param {number} index - The index of the city to remove.
 */
const removeState = (index: number) => {
    form.states.splice(index, 1);
};


// --- Form Submission ---
const onSubmit = () => {

    form.post(route('state.bulk.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'State have been created successfully!', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Please check the form for errors.', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        },
    });
}

watch(() => selectedCountry.value, (newValue) => {
    if (newValue) {
        form.country_id = newValue.id;
    } else {
        form.country_id = '';
    }
});
</script>

<template>
    <Head title="Bulk Create States" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <Breadcrumb :home="home" :model="items" />
        <div class="p-10 pt-5">
            <form @submit.prevent="onSubmit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-b border-gray-300 pb-8">
                    <div>
                        <label for="country_id" class="font-semibold block mb-2">Country <span class="text-red-500">*</span></label>
                        <Select
                            id="country_id"
                            class="w-full"
                            v-model="selectedCountry"
                            :options="countries.data"
                            filter
                            optionLabel="name"
                            placeholder="Select a Country"
                            :invalid="!!errors.country_id"
                        />
                        <Message v-if="errors.country_id" severity="error" size="small" variant="simple">{{ errors.country_id }}</Message>
                    </div>
                </div>
                <div class="relative border border-gray-300 p-2 bg-gray-100 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            <label  class="font-normal block mb-2 text-sm">State Name <span class="text-red-500">*</span></label>
                            <label  class="font-normal block mb-2 text-sm">Shipping Charge</label>
                            <label  class="font-normal block mb-2 text-sm">Tax %</label>
                            <label  class="font-normal block mb-2 text-sm">VAT %</label>
                    </div>
                    <div v-for="(city, index) in form.states" :key="index">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-2">
                            <div>
                                <InputText
                                    :id="`name-${index}`"
                                    class="w-full"
                                    v-model="city.name"
                                    :invalid="!!errors[`states.${index}.name`]"
                                    autocomplete="off"
                                />
                                <Message v-if="errors[`states.${index}.name`]" severity="error" size="small" variant="simple">{{ errors[`states.${index}.name`] }}</Message>
                            </div>

                            <div>
                                <InputNumber
                                    :id="`shipping_charge-${index}`"
                                    class="w-full"
                                    currency="USD"
                                    mode="currency"
                                    v-model="city.shipping_charge"
                                    :invalid="!!errors[`states.${index}.shipping_charge`]"
                                />
                                <Message v-if="errors[`states.${index}.shipping_charge`]" severity="error" size="small" variant="simple">{{ errors[`states.${index}.shipping_charge`] }}</Message>
                            </div>

                            <div>
                                <InputNumber
                                    :id="`tax_percentage-${index}`"
                                    class="w-full"
                                    v-model="city.tax_percentage"
                                    suffix="%"
                                    :invalid="!!errors[`states.${index}.tax_percentage`]"
                                />
                                <Message v-if="errors[`states.${index}.tax_percentage`]" severity="error" size="small" variant="simple">{{ errors[`states.${index}.tax_percentage`] }}</Message>
                            </div>

                            <div>
                                <InputNumber
                                    :id="`vat_percentage-${index}`"
                                    class="w-full"
                                    v-model="city.vat_percentage"
                                    suffix="%"
                                    :invalid="!!errors[`states.${index}.vat_percentage`]"
                                />
                                <Message v-if="errors[`states.${index}.vat_percentage`]" severity="error" size="small" variant="simple">{{ errors[`states.${index}.vat_percentage`] }}</Message>
                            </div>
                        </div>

                        <div class="absolute top-2 right-2">
                            <Button
                                v-if="form.states.length > 1"
                                type="button"
                                icon="pi pi-times"
                                severity="danger"
                                text
                                rounded
                                aria-label="Remove"
                                @click="removeState(index)"
                            />
                        </div>
                    </div>
                 </div>

                 <div class="flex justify-between items-center mt-8">
                    <Button
                        type="button"
                        label="Add Another State"
                        icon="pi pi-plus"
                        severity="secondary"
                        @click="addState"
                    />
                    <Button
                        type="submit"
                        label="Save All States"
                        icon="pi pi-save"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </div>
    </div>
</template>