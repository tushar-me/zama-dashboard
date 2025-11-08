<script setup lang="ts">
import { ref,computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import moment from 'moment';

defineOptions({ layout: AppLayout });
const toast = useToast();
const props = defineProps<{
    badges: any[],
    current_page: number,
    errors: any,
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Badge' }
]);

const op = ref();



const toggle = (event) => {
       op.value.toggle(event);
}

const rows = ref(10) 
const first = ref(props.current_page)

function onPageChange(event: any) {
    const currentPage = event.page + 1 
    rows.value = event.rows
    first.value = event.first
    router.get(route('category.index'), {
        page: currentPage,
        per_page: event.rows,
    })
}

const createDialog = ref(false);
const onFileSelect = (event) => {
    const file = event.files[0];
    const reader = new FileReader();
    reader.onload = (e) => {
        form.logo = e.target.result; 
    }
    reader.readAsDataURL(file);
}
const form = useForm({
    name: '',
    order_level: 0,
    logo: null,
    type: 'fixed',
    adjustment_type: 'add',
    adjustment_value: 0,
    status: true,
})

const onSubmit = () => {
    form.post('/badge', {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Badge has been created successfully', life: 3000 });
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
    })
}

const selectedBadges = ref([]);
    
</script>

<template>
    <Head title="Badge" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="badges?.data" v-model:selection="selectedBadges">
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
                        <Button  icon="pi pi-plus" @click="createDialog = true" label="Create Badge" />
                    </div>
                </div>
                <Paginator
                    :rows="rows"
                
                    :totalRecords="badges?.total"
                    :rowsPerPageOptions="[10, 20, 30]"
                    @page="onPageChange"
                >
                    <template #start="slotProps">
                    Page: {{ slotProps.state.page + 1 }}
                    First: {{ slotProps.state.first }}
                    Rows: {{ slotProps.state.rows }}
                    </template>
                </Paginator>
                <ul v-if="selectedBadges.length > 0" class="flex items-center gap-3 px-4">
                    <li v-if="selectedBadges.length == 1">
                        <Button icon="pi pi-pen-to-square" label="Edit" size="small" @click="deleteBadges" />
                    </li>
                    <li>
                        <Button icon="pi pi-trash" label="Delete" size="small" severity="danger" @click="deleteBadges" />
                    </li>
                </ul>
            </template>
            <template #empty> No category found. </template>
            <template #loading> Loading category data. Please wait. </template>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="name" header="Name" style="min-width: 10rem" sortable>
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="logo" header="Logo" style="min-width: 8rem">
                <template #body="{ data }">
                    <img class="size-20 object object-contain" :src="data?.logo" :alt="data?.name">
                </template>
            </Column>
             <Column field="created_by" header="Creator" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.creator?.name }}</p>
                    <p class="text-sm font-normal">{{ moment(data?.created_at).format('MMMM Do YYYY, h:mm a') }}</p>
                </template>
            </Column>
            <Column field="last_updated_at" header="Last Updated By" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.editor?.name }}</p>
                    <p class="text-sm font-normal">{{ moment(data?.updated_at).format('MMMM Do YYYY, h:mm a') }}</p>
                </template>
            </Column>
            <Column field="status" header="Status" style="min-width: 5rem">
                <template #body="{ data }">
                    {{ data?.status }}
                </template>
            </Column>
             <Column field="order_level" header="Order Level">
                <template #body="{ data }">
                    {{ data?.order_level }}
                </template>
            </Column>
            <Column>
                <template #body="{ data }">
                     <Button type="button" variant="text" icon="pi pi-ellipsis-v" @click="toggle" />
                    <Popover ref="op">
                        <div class="flex flex-col w-[10rem]">
                            <Link href="/vendor/" class="flex items-center gap-2 py-2 px-3 text-gray-700 hover:bg-stone-100 rounded-lg">
                                <Icon name="ic:outline-remove-red-eye"/>
                                View
                            </Link>
                            <Link href="/vendor/" class="flex items-center gap-2 py-2 px-3 text-gray-700 hover:bg-stone-100 rounded-lg">
                                <Icon name="material-symbols:edit-square-outline-rounded"/>
                                Edit
                            </Link>
                            <button @click="requireConfirmation(data.id)" class="flex items-center gap-2 py-2 px-3 text-gray-700 hover:bg-red-100 hover:text-red-500 rounded-lg">
                                <Icon name="mdi-light:delete"/>
                                Delete
                            </button>
                             <ConfirmDialog>
                            <template #container="{ message, acceptCallback, rejectCallback }">
                                <div class="flex flex-col py-6 px-7 bg-white">
                                    <div class="flex items-center gap-1 mb-3">
                                       <Icon name="ph:warning-circle" class="text-4xl"/>
                                        <span class="font-bold text-xl block">{{ message.header }}</span>
                                    </div>
                                    <p>{{ message.message }}</p>
                                    <div class="flex items-center justify-end gap-2 mt-6">
                                        <Button label="Cancel" outlined size="small" @click="rejectCallback"></Button>
                                        <Button label="Delete" severity="danger" size="small" @click="acceptCallback"></Button>
                                    </div>
                                </div>
                            </template>
                        </ConfirmDialog>
                        </div>
                    </Popover>
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- Create Category Dialog -->
     <Dialog v-model:visible="createDialog" modal header="Edit Profile" :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Create Badge</span>
                </div>
            </template>
            <form @submit.prevent="onSubmit" class="p-3">
                <div class="flex items-center  gap-4 mb-4">
                    <label for="name" class="font-semibold lg:w-1/4">Name</label>
                    <div class="w-full lg:w-3/4">
                        <InputText v-model="form.name" id="name" class="w-full" autocomplete="off" :invalid="errors.name" />
                        <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="type" class="font-semibold lg:w-1/4">Type</label>
                    <div class="w-full lg:w-3/4">
                        <SelectButton v-model="form.type" :options="['fixed', 'percentage']" :invalid="errors.type" />
                        <Message v-if="errors.type" severity="error" size="small" variant="simple">{{ errors.type }}</Message>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="adjustment_type" class="font-semibold lg:w-1/4">Adjustment Type</label>
                    <div class="w-full lg:w-3/4">
                        <SelectButton v-model="form.adjustment_type" :options="['add', 'substract']" :invalid="errors.adjustment_type" />
                        <Message v-if="errors.adjustment_type" severity="error" size="small" variant="simple">{{ errors.adjustment_type }}</Message>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label for="adjustment_value" class="font-semibold lg:w-1/4">Adjustment Value</label>
                    <div class="w-full lg:w-3/4">
                        <InputNumber v-if="form.type == 'fixed'" v-model="form.adjustment_value" showButtons mode="currency" currency="USD" :invalid="errors.adjustment_value" />
                        <InputNumber v-else v-model="form.adjustment_value" prefix="%" showButtons :invalid="errors.adjustment_value" />
                        <Message v-if="errors.adjustment_value" severity="error" size="small" variant="simple">{{ errors.adjustment_value }}</Message>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-2">
                    <label for="order_level" class="font-semibold lg:w-1/4">Order Level</label>
                    <div class="w-full lg:w-3/4">
                        <InputNumber v-model="form.order_level" id="order_level" class="flex-auto" autocomplete="off" :invalid="errors.order_level" />
                        <Message v-if="errors.order_level" severity="error" size="small" variant="simple">{{ errors.order_level }}</Message>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-2">
                    <label for="imaage" class="font-semibold lg:w-1/4">Logo</label>
                   <div class="w-full lg:w-3/4">
                        <FileUpload name="demo[]" url="/upload"  :auto="true" @select="onFileSelect"  accept="image/*" :maxFileSize="1000000">
                            <template #empty>
                                <span>Drag and drop files to here to upload.</span>
                            </template>
                        </FileUpload>
                        <span v-if="errors.logo" class="text-red-500">{{ errors.logo }}</span>
                   </div>
                </div>
                <div class="flex items-center gap-4 mb-2">
                    <label for="status" class="font-semibold lg:w-1/4">Status</label>
                    <div class="w-full lg:w-3/4">
                        <!-- <ToggleSwitch v-model="toggleStatus" /> -->
                        <Message v-if="errors.status" severity="error" size="small" variant="simple">{{ errors.status }}</Message>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <Button label="Cancel" text severity="secondary" @click="createDialog = false" autofocus />
                    <Button type="submit" :disabled="form.processing" label="Save" autofocus />
                </div>
            </form>
        </Dialog>
    <!-- Create Category Dialog End -->
</template>
