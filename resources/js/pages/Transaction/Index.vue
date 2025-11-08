<script setup lang="ts">
import { ref,computed, reactive } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { Order } from '@/types';
import { watch } from 'vue';

defineOptions({ layout: AppLayout });


const toast = useToast();
const props = defineProps<{
    orders: {
        data: Order[],
        total: number,
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
    { label: 'Order' }
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

//edit mockup side
const selectedMockups = ref([]);
const selectedMockup = ref<Mockup | null>(null);
const cm = ref();
const menuModel = ref([
    {label: 'Edit', icon: 'pi pi-pen-to-square', command: () => edit(selectedMockup.value?.id)},
]);
const onRowContextMenu = (event: any) => {
    cm.value.show(event.originalEvent);
};
const edit = (id?: string) => {
    router.visit(route('mockup.edit', id));
}
</script>

<template>
    <Head title="Orders" />
    <Toast />
    <ContextMenu ref="cm" :model="menuModel" />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="orders?.data" v-model:selection="selectedMockups" contextMenu v-model:contextMenuSelection="selectedMockup"  @rowContextmenu="onRowContextMenu">
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
                    </div>
                </div>
                <Paginator
                    :rows="rows"
                
                    :totalRecords="orders?.total"
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
            <template #empty> No mockup found. </template>
            <template #loading> Loading mockup data. Please wait. </template>
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
                    {{ data?.creator?.name }}
                </template>
            </Column>
            <Column field="last_updated_at" header="Last Updated By" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.editor?.name }}
                </template>
            </Column>
            <Column field="status" header="Status" style="min-width: 5rem">
                <template #body="{ data }">
                    {{ data?.status }}
                </template>
            </Column>
            <Column field="type" header="Type">
                <template #body="{ data }">
                    <span v-if="data?.type == 'default'" class="uppercase text-sm bg-slate-300 p-2 rounded-md">{{ data?.type }}</span>
                    <span v-else class="uppercase text-sm bg-amber-300 p-2 rounded-md">{{ data?.type }}</span>
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
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>
