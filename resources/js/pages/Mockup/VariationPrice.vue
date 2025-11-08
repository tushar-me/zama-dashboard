<script setup lang="ts">
import { ref, computed, reactive } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { MockupVariation, Mockup, Size } from '@/types'; 
import { watchDebounced } from '@vueuse/core';



defineOptions({ layout: AppLayout });

const toast = useToast();
const props = defineProps<{
    variations:{
        data: MockupVariation[],
        current_page: number,
        per_page: number,
        total: number,
    },
    mockup: Mockup,
    sizes: Size[],
    search: string,
    errors: any
}>();

const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Mockup', url: route('mockup.index') },
    { label: props.mockup.name, url: route('mockup.edit', props.mockup.id) },
    { label: 'Variations' }
]);

const search = reactive<{ value: string }>({ value: props.search ?? '' });
watchDebounced(search, () => {
    getVariations();
}, { debounce: 500 });

const getVariations = () => {
    router.get(route("variation-price.index"), { mockup: props.mockup.id, search: search.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};

function onPageChange(event: any) {
    const currentPage = event.page + 1;
    router.get(route("variation.price.index"), {
        mockup: props.mockup.id,
        page: currentPage,
        limit: event.rows,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

// Add New Variation
const visibleAddModal = ref(false);
const addForm = useForm({
    mockup_id: props.mockup.id,
    size_id: null,
    sku: '',
    price: null,
    sell_price: null,
    status: true,
});

const openAddModal = () => {
    addForm.reset();
    visibleAddModal.value = true;
};

const storeVariation = () => {
    addForm.post(route('variation-price.store'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Mockup variation created successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleAddModal.value = false;
            addForm.reset();
            getVariations();
        },
        onError: (errors) => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
            console.error(errors);
        },
    });
};

// Edit Variation
const visibleEditModal = ref(false);
const selectedVariation = ref<MockupVariation | null>(null);
const editForm = useForm({
    mockup_id: '' as string | null,
    size_id: null as string | null,
    sku: '' as string,
    price: null as number | null,
    sell_price: null as number | null,
    status: true as boolean,
});

const openEditModal = (variation: MockupVariation) => {
    selectedVariation.value = variation;
    editForm.mockup_id = variation.mockup_id;
    editForm.size_id = variation.size_id;
    editForm.sku = variation.sku;
    editForm.price = variation.price;
    editForm.sell_price = variation.sell_price;
    editForm.status = variation.status;
    visibleEditModal.value = true;
};

const updateVariation = () => {
    if (selectedVariation.value) {
        editForm.put(route('variation-price.update', selectedVariation.value.id), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Success', detail: 'Mockup variation updated successfully', life: 3000 });
                const audio = new Audio('/success.wav');
                audio.play();
                visibleEditModal.value = false;
                selectedVariation.value = null;
                editForm.reset();
                getVariations();
            },
            onError: (errors) => {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
                const audio = new Audio('/error.wav');
                audio.play();
                console.error(errors);
            },
        });
    }
};

// Delete Variation
const visibleDeleteDialog = ref(false);
const variationToDelete = ref<MockupVariation | null>(null);

const confirmDelete = (variation: MockupVariation) => {
    variationToDelete.value = variation;
    visibleDeleteDialog.value = true;
};

function onDelete() {
    if (variationToDelete.value) {
        router.delete(route('variation-price.destroy', variationToDelete.value.id), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Success', detail: 'Mockup variation has been deleted successfully', life: 3000 });
                const audio = new Audio('/success.wav');
                audio.play();
                visibleDeleteDialog.value = false;
                variationToDelete.value = null;
                getVariations();
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
                const audio = new Audio('/error.wav');
                audio.play();
            },
        });
    }
}
</script>

<template>
    <Head :title="`Mockup Variations for ${mockup.name}`" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="variations.data">
            <template #header>
                <div class="flex justify-between items-center">
                    <Breadcrumb :home="home" :model="items" />
                    <div class="flex items-center gap-2">
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="search.value" placeholder="Search" />
                        </IconField>
                        <Button asChild v-slot="slotProps">
                            <button @click="openAddModal" :class="slotProps.class">
                                <Icon name="material-symbols:add-rounded" />
                                Add New Variation
                            </button>
                        </Button>
                    </div>
                </div>
            
                <Paginator
                    :rows="variations.per_page ?? 10"
                    :first="(variations.current_page - 1) * variations.per_page"
                    :totalRecords="variations.total"
                    :rowsPerPageOptions="[10, 20, 30]"
                    @page="onPageChange"
                />
            </template>
            <template #empty> No mockup variations found. </template>
            <template #loading> Loading mockup variations data. Please wait. </template>
            <Column field="size.name" header="Size" style="min-width: 8rem">
                <template #body="{ data }">
                    {{ data?.size?.name }}
                </template>
            </Column>
            <Column field="sku" header="SKU" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.sku }}
                </template>
            </Column>
            <Column field="price" header="Price" style="min-width: 8rem">
                <template #body="{ data }">
                    {{ data?.price }}
                </template>
            </Column>
            <Column field="sell_price" header="Sell Price" style="min-width: 8rem">
                <template #body="{ data }">
                    {{ data?.sell_price }}
                </template>
            </Column>
            <Column field="status" header="Status" style="min-width: 5rem">
                <template #body="{ data }">
                    <span :class="{'bg-green-500': data?.status, 'bg-red-500': !data?.status}" class="text-white p-1 rounded-md text-xs">
                        {{ data?.status ? 'Active' : 'Inactive' }}
                    </span>
                </template>
            </Column>
            <Column header="Actions" style="min-width: 10rem">
                <template #body="{ data }">
                    <div class="flex items-center">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openEditModal(data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDelete(data)" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- Add New Variation Modal -->
    <Dialog v-model:visible="visibleAddModal" modal :style="{ width: '30rem' }" @hide="addForm.reset()">
        <template #header>
            <div class="inline-flex items-center justify-center gap-2">
                <span class="font-bold whitespace-nowrap">Add New Mockup Variation</span>
            </div>
        </template>
        <form @submit.prevent="storeVariation">
            <div>
                <div class="mb-4">
                    <label for="name" class="font-semibold">Size</label>
                    <Dropdown
                        id="add-size"
                        v-model="addForm.size_id"
                        :options="sizes"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Select a Size"
                        class="w-full"
                        :invalid="errors.size_id"
                    />
                    <Message v-if="errors.size_id" severity="error" size="small" variant="simple">{{ errors?.size_id }}</Message>
                </div>
                <div class="mb-4">
                    <label for="price" class="font-semibold">Price</label>
                    <InputNumber
                        id="add-price"
                        v-model="addForm.price"
                        mode="currency" currency="USD" locale="en-US" 
                        class="w-full"
                        :invalid="errors.price"
                    />
                    <Message v-if="errors.price" severity="error" size="small" variant="simple">{{ errors?.price }}</Message>
                </div>
                <div class="mb-4">
                    <label for="price" class="font-semibold">Sell Price</label>
                    <InputNumber
                        id="add-sell-price"
                        v-model="addForm.sell_price"
                        mode="currency" currency="USD" locale="en-US" 
                        class="w-full"
                        required
                        :invalid="errors.sell_price"
                    />
                    <Message v-if="errors.sell_price" severity="error" size="small" variant="simple">{{ errors?.sell_price }}</Message>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" text severity="secondary" @click="visibleAddModal = false" />
                <Button :disabled="addForm.processing" type="submit">Add Variation</Button>
            </div>
        </form>
    </Dialog>

    <!-- Edit Variation Modal -->
    <Dialog v-model:visible="visibleEditModal" modal :style="{ width: '30rem' }" @hide="selectedVariation = null, editForm.reset()">
        <template #header>
            <div class="inline-flex items-center justify-center gap-2">
                <span class="font-bold whitespace-nowrap">Edit Mockup Variation</span>
            </div>
        </template>
        <form @submit.prevent="updateVariation">
            <div>
                <div class="mb-4">
                    <label for="name" class="font-semibold">Size</label>
                    <Dropdown
                        id="add-size"
                        v-model="editForm.size_id"
                        :options="sizes"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Select a Size"
                        class="w-full"
                        :invalid="errors.size_id"
                    />
                    <Message v-if="errors.size_id" severity="error" size="small" variant="simple">{{ errors?.size_id }}</Message>
                </div>
                <div class="mb-4">
                    <label for="price" class="font-semibold">Price</label>
                    <InputNumber
                        id="add-price"
                        v-model="editForm.price"
                        mode="currency" currency="USD" locale="en-US" 
                        class="w-full"
                        :invalid="errors.price"
                    />
                    <Message v-if="errors.price" severity="error" size="small" variant="simple">{{ errors?.price }}</Message>
                </div>
                <div class="mb-4">
                    <label for="price" class="font-semibold">Sell Price</label>
                    <InputNumber
                        id="add-sell-price"
                        v-model="editForm.sell_price"
                        mode="currency" currency="USD" locale="en-US" 
                        class="w-full"
                        required
                        :invalid="errors.sell_price"
                    />
                    <Message v-if="errors.sell_price" severity="error" size="small" variant="simple">{{ errors?.sell_price }}</Message>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" text severity="secondary" @click="visibleEditModal = false" />
                <Button :disabled="editForm.processing" type="submit">Update Variation</Button>
            </div>
        </form>
    </Dialog>

    <!-- Delete Dialog -->
    <Dialog v-model:visible="visibleDeleteDialog" modal :style="{ width: '30rem' }" @hide="variationToDelete = null">
        <template #header>
            <div class="inline-flex items-center justify-center gap-2">
                <span class="font-bold whitespace-nowrap">Delete Mockup Variation</span>
            </div>
        </template>
        <div class="p-4 flex flex-col items-center gap-4">
            <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
            </span>
            <p class="text-center">Are you sure you want to delete SKU: <span class="font-bold text-black">{{ variationToDelete?.sku }}</span>? This action cannot be undone.</p>
        </div>
        <div class="flex justify-end gap-2 p-4">
            <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
            <Button @click="onDelete()" severity="danger" label="Yes Delete" autofocus />
        </div>
    </Dialog>
</template>
