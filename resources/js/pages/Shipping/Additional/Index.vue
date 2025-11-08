<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { AdditionalShippingCharge } from '@/types';
import Icon from '@/Components/Icon.vue';

defineOptions({ layout: AppLayout });

const confirm = useConfirm();
const toast = useToast();

const props = defineProps<{
    additionalShippingCharges: {
        data: AdditionalShippingCharge[],
        current_page: number,
        total: number,
        per_page: number,
    },
    current_page: number,
    errors: any,
}>();

const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Additional Shipping Charges' }
]);

const rows = ref(props.additionalShippingCharges.per_page || 10);
const first = ref(props.additionalShippingCharges.current_page);

function onPageChange(event: any) {
    const currentPage = event.page + 1;
    rows.value = event.rows;
    first.value = event.first;
    router.get(route('additional-shipping-charge.index'), {
        page: currentPage,
        per_page: event.rows,
    });
}

const createDialog = ref(false);
const visibleEditDialog = ref(false);
const visibleDeleteDialog = ref(false);

const selectedAdditionalShippingCharge = ref<AdditionalShippingCharge | null>(null);

const form = useForm({
    name: '',
    description: null,
    us_charge: 0.00,
    worldwide_charge: 0.00,
    status: true,
    order_level: 0,
});

watch(() => selectedAdditionalShippingCharge.value, (newVal: AdditionalShippingCharge | null) => {
    if (newVal) {
        form.name = newVal.name;
        form.description = newVal.description;
        form.us_charge = newVal.us_charge;
        form.worldwide_charge = newVal.worldwide_charge;
        form.status = newVal.status;
        form.order_level = newVal.order_level;
    } else {
        form.reset();
    }
});

const onSubmit = () => {
    form.post(route('additional-shipping-charge.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Additional Shipping Charge has been created successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            createDialog.value = false;
            form.reset();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        }
    });
};

const onUpdate = () => {
    router.put(route('additional-shipping-charge.update', selectedAdditionalShippingCharge.value?.id), {
        name: form.name,
        description: form.description,
        us_charge: form.us_charge,
        worldwide_charge: form.worldwide_charge,
        status: form.status,
        order_level: form.order_level,
    },
    {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Additional Shipping Charge has been updated successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleEditDialog.value = false;
            selectedAdditionalShippingCharge.value = null;
            form.reset();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        }
    });
};

const onDelete = (id: string | undefined) => {
    router.delete(route('additional-shipping-charge.destroy', id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Additional Shipping Charge has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedAdditionalShippingCharge.value = null;
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        }
    });
};
</script>

<template>
    <Head title="Additional Shipping Charges" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="additionalShippingCharges?.data">
            <template #header>
                <div class="flex justify-between items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText placeholder="Search" />
                        </IconField>
                        <Button @click="createDialog = true">Create Additional Shipping Charge</Button>
                    </div>
                </div>
                <Paginator
                    :rows="rows"
                    :totalRecords="additionalShippingCharges?.total"
                    :rowsPerPageOptions="[10, 20, 30]"
                    @page="onPageChange"
                >
                    <template #start="slotProps">
                    Page: {{ slotProps.state.page + 1 }}
                    First: {{ slotProps.state.first }}
                    Rows: {{ slotProps.state.rows }}
                    </template>
                </Paginator>
            </template>
            <template #empty> No additional shipping charges found. </template>
            <template #loading> Loading additional shipping charges data. Please wait. </template>
            <Column field="name" header="Name" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="description" header="Description" style="min-width: 15rem">
                <template #body="{ data }">
                    {{ data?.description }}
                </template>
            </Column>
            <Column field="us_charge" header="US Charge" style="min-width: 8rem">
                <template #body="{ data }">
                    {{ data?.us_charge }}
                </template>
            </Column>
            <Column field="worldwide_charge" header="Worldwide Charge" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.worldwide_charge }}
                </template>
            </Column>
            <Column field="status" header="Status" style="min-width: 5rem">
                <template #body="{ data }">
                    {{ data?.status ? 'Active' : 'Inactive' }}
                </template>
            </Column>
             <Column field="order_level" header="Order Level">
                <template #body="{ data }">
                    {{ data?.order_level }}
                </template>
            </Column>
            <Column field="created_by" header="Creator" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.creator?.name }}
                </template>
            </Column>
            <Column field="last_updated_by" header="Last Updated By" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.editor?.name }}
                </template>
            </Column>
            <Column>
                <template #body="{ data }">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="selectedAdditionalShippingCharge = data, visibleEditDialog = true" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedAdditionalShippingCharge = data, visibleDeleteDialog=true" />
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- Create Additional Shipping Charge Dialog -->
     <Dialog v-model:visible="createDialog" modal :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Create Additional Shipping Charge</span>
                </div>
            </template>
            <form @submit.prevent="onSubmit">
                <div class="flex items-center gap-4 mb-4">
                    <label for="name" class="font-semibold w-24">Name</label>
                    <InputText v-model="form.name" id="name" class="flex-auto" autocomplete="off" />
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="description" class="font-semibold w-24">Description</label>
                    <Textarea v-model="form.description" id="description" rows="3" class="flex-auto" />
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="us_charge" class="font-semibold w-24">US Charge</label>
                    <InputNumber v-model="form.us_charge" id="us_charge" mode="decimal" :minFractionDigits="2" :maxFractionDigits="2" class="flex-auto" autocomplete="off" />
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="worldwide_charge" class="font-semibold w-24">Worldwide Charge</label>
                    <InputNumber v-model="form.worldwide_charge" id="worldwide_charge" mode="decimal" :minFractionDigits="2" :maxFractionDigits="2" class="flex-auto" autocomplete="off" />
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="order_level" class="font-semibold w-24">Order Level</label>
                    <InputNumber v-model="form.order_level" id="order_level" class="flex-auto" autocomplete="off" />
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="status" class="font-semibold w-24">Status</label>
                    <InputSwitch v-model="form.status" id="status" />
                </div>
                <button type="submit" class="hidden" aria-hidden="true"></button>
            </form>
            <template #footer>
                <Button label="Cancel" text severity="secondary" @click="createDialog = false" autofocus />
                <Button @click="onSubmit" :disabled="form.processing" label="Save" autofocus />
            </template>
        </Dialog>
    <!-- Create Additional Shipping Charge Dialog End -->

    <!-- Edit Additional Shipping Charge Dialog -->
    <Dialog v-model:visible="visibleEditDialog" modal :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" @hide="selectedAdditionalShippingCharge = null">
        <template #header>
            <div class="inline-flex items-center justify-center gap-2">
                <span class="font-bold whitespace-nowrap">Edit {{ selectedAdditionalShippingCharge?.name }}</span>
            </div>
        </template>
        <form @submit.prevent="onUpdate">
            <div class="flex items-center gap-4 mb-4">
                <label for="edit-name" class="font-semibold w-24">Name</label>
                <InputText v-model="form.name" id="edit-name" class="flex-auto" autocomplete="off" />
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="edit-description" class="font-semibold w-24">Description</label>
                <Textarea v-model="form.description" id="edit-description" rows="3" class="flex-auto" />
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="edit-us_charge" class="font-semibold w-24">US Charge</label>
                <InputNumber v-model="form.us_charge" id="edit-us_charge" mode="decimal" :minFractionDigits="2" :maxFractionDigits="2" class="flex-auto" autocomplete="off" />
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="edit-worldwide_charge" class="font-semibold w-24">Worldwide Charge</label>
                <InputNumber v-model="form.worldwide_charge" id="edit-worldwide_charge" mode="decimal" :minFractionDigits="2" :maxFractionDigits="2" class="flex-auto" autocomplete="off" />
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="edit-order_level" class="font-semibold w-24">Order Level</label>
                <InputNumber v-model="form.order_level" id="edit-order_level" class="flex-auto" autocomplete="off" />
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label for="edit-status" class="font-semibold w-24">Status</label>
                <InputSwitch v-model="form.status" id="edit-status" />
            </div>
            <button type="submit" class="hidden" aria-hidden="true"></button>
        </form>
        <template #footer>
            <Button label="Cancel" text severity="secondary" @click="visibleEditDialog = false" autofocus />
            <Button @click="onUpdate" :disabled="form.processing" label="Save" autofocus />
        </template>
    </Dialog>
</template>
