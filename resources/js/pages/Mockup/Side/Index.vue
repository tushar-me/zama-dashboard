<script setup lang="ts">
import { ref,computed, reactive } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { MockupSide, Mockup } from '@/types';
import { watch } from 'vue';
import moment from 'moment';

defineOptions({ layout: AppLayout });


const toast = useToast();
const props = defineProps<{
    mockupSides: {
        data: MockupSide[], 
        total: number,
    },
    mockups: {
        data: Mockup[]
    },
    search: string,
    column: string,
    limit: number,
    page: number,
    current_page: number,
    errors: any,
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Mockup'},
    { label: 'Side'}
]);

const search = reactive<{ value: string }>({ value: props.search ?? '' });
const column = reactive<{ value: string }>({ value: props.column ?? 'order_level' });
const limit = reactive<{ value: number }>({ value: props.limit ?? 10 });
const page = reactive<{ value: number }>({ value: props.page ?? 1 });

const getMockups = () => {
    router.get(route('mockup.index'), { search, column, limit, page});
}

watch(search, getMockups, { deep: true });
watch(column, getMockups, { deep: true });
watch(limit, getMockups, { deep: true });
watch(page, getMockups, { deep: true });


const op = ref();
const toggle = (event: Event) => {
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

// Select MOckup
const visibleMockupSelectDialog = ref<boolean>(false);
const searchMockup = ref<string>('');
const filteredMockups = computed(() => {
    return props.mockups?.data?.filter((mockup: Mockup) => mockup.name.toLowerCase().includes(searchMockup.value.toLowerCase())) ?? [];
});
//edit mockup side
const selectedMockupSides = ref([]);
const selectedMockupSide = ref<MockupSide | null>(null);
const cm = ref();
const menuModel = ref([
    {label: 'Edit', icon: 'pi pi-pen-to-square', command: () => edit(selectedMockupSide.value?.id)},
    {label: 'Delete', icon: 'pi pi-fw pi-trash', command: () => visibleDeleteDialog.value = true}
]);
const onRowContextMenu = (event: any) => {
    cm.value.show(event.originalEvent);
};

const edit = (id?: string) => {
    router.visit(route('mockup-side.edit', id));
}
// Delete Mockup
const visibleDeleteDialog = ref(false);
function onDelete() {
    router.delete(route('mockup-side.destroy', selectedMockupSide.value?.id), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Mockup has been deleted successfully', life: 3000 });
            const audio = new Audio('/success.wav');
            audio.play();
            visibleDeleteDialog.value = false;
            selectedMockupSide.value = null;
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong!', life: 3000 });
            const audio = new Audio('/error.wav');
            audio.play();
        },
    });
}
</script>

<template>
    <Head title="Mockup Side" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="mockupSides?.data" v-model:selection="selectedMockupSides" contextMenu v-model:contextMenuSelection="selectedMockupSide"  @rowContextmenu="onRowContextMenu">
            <template #header>
                <div class="flex flex-col lg:flex-row justify-between lg:items-center">
                   <Breadcrumb :home="home" :model="items" />
                    <div class="flex flex-col lg:flex-row lg:items-center gap-2">
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText placeholder="Search" class="w-full" />
                        </IconField>
                        <Button @click="visibleMockupSelectDialog = true" icon="pi pi-plus" label="Create Mockup Side" />
                    </div>
                </div>
                <Paginator
                    :rows="rows"
                
                    :totalRecords="categories?.total"
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
            <template #empty> No category found. </template>
            <template #loading> Loading category data. Please wait. </template>
            <Column field="name" header="Name" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="image" header="Image" style="min-width: 8rem">
                <template #body="{ data }">
                    <img class="size-20 object object-contain" :src="data?.image" :alt="data?.name">
                </template>
            </Column>
            <Column field="created_by" header="Creator" style="min-width: 10rem">
                <template #body="{ data }">
                    <p class="text-sm font-normal">{{ data?.creator?.name }}</p>
                    <p class="text-sm font-normal">{{ moment(data?.created_at).format('MMMM Do YYYY, h:mm a') }}</p>
                </template>
            </Column>
            <Column field="last_updated_by" header="Last Updated By" style="min-width: 10rem">
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
                  <div class="flex items-center">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="edit(data?.id)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="selectedMockupSide = data, visibleDeleteDialog=true" />
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
    <!-- Mockup Select Dialog -->
    <Dialog v-model:visible="visibleMockupSelectDialog" position="top"  modal :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <template #header>
            <div class="inline-flex items-center justify-center gap-2">
                <span class="font-bold whitespace-nowrap">Select Mockup</span>
            </div>
        </template>
        <div>
            <div class="mb-4">
                <IconField class="w-full">
                    <InputIcon class="pi pi-search" />
                    <InputText v-model="searchMockup" placeholder="Search" class="w-full" />
                </IconField>

            </div>
           <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-4">
            <Link :href="`/mockup-side/create?mockup=${mockup?.id}`" v-for="mockup in filteredMockups" :key="mockup.id" 
            class="focus:ring-0 focus:outline-none focus:border-primary focus:shadow-lg block bg-white hover:bg-gray-50 hover:shadow-lg hover:border-primary border border-gray-200 rounded-lg p-2">
                <img :src="mockup?.image" :alt="mockup?.name" class="w-full h-auto object-cover rounded-lg">
                 <div class="p-1"> 
                    <h3 class="text-sm font-bold">{{ mockup?.name }}</h3>
                 </div>
            </Link>
           </div>
        </div>
    </Dialog>
    <!-- Delete Dialog -->
     <Dialog v-model:visible="visibleDeleteDialog" modal  :style="{ width: '30rem' }" @hide="selectedMockupSide = null">
        <template #header>
                <div class="inline-flex items-center justify-center gap-2">
                    <span class="font-bold whitespace-nowrap">Delete {{ selectedMockupSide?.name }} Country</span>
                </div>
            </template>
            <div class="p-4 flex flex-col items-center gap-4">
                <span class="size-16 bg-red-100 rounded-full flex items-center justify-center">
                    <Icon name="qlementine-icons:warning-16" class="text-3xl text-red-500" />
                </span>
                <p class="text-center">Are you sure you want to delete <span class="font-bold text-black">{{ selectedMockupSide?.name }}</span> Country ? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-2  p-4">
                <Button label="Cancel" text severity="secondary" @click="visibleDeleteDialog = false" autofocus />
                <Button @click="onDelete()" severity="danger" label="Yes Delete" autofocus />
            </div>
    </Dialog>
</template>
