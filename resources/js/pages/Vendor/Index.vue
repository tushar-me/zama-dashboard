<script setup lang="ts">
import { ref,computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
defineOptions({ layout: AppLayout });
const confirm = useConfirm();
const toast = useToast();
const props = defineProps<{
    vendors: any[],
    current_page: number,
}>()
const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home'
});
const items = ref([
    { label: 'Vendors' }
]);

const op = ref();
const requireConfirmation = () => {
    confirm.require({
        message: 'Do you want to delete this record?',
        header: 'Danger Zone',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Cancel',
        accept: () => {
            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'You have accepted', life: 3000 });
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });
};
const toggle = (event) => {
       op.value.toggle(event);
}

const rows = ref(10) 
const first = ref(props.current_page)

function onPageChange(event: any) {
    const currentPage = event.page + 1 
    rows.value = event.rows
    first.value = event.first
    router.get(route('vendor.index'), {
        page: currentPage,
        per_page: event.rows,
    })
}
</script>

<template>
    <Head title="Vendor" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <DataTable :value="vendors?.data">
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
                        <Button>Create Vendor</Button>
                    </div>
                </div>
                <Paginator
                    :rows="rows"
                
                    :totalRecords="vendors?.total"
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
            <template #empty> No customers found. </template>
            <template #loading> Loading customers data. Please wait. </template>
            <Column field="name" header="Name" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.name }}
                </template>
            </Column>
            <Column field="phone" header="Phone" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.phone }}
                </template>
            </Column>
            <Column field="email" header="Email" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.email }}
                </template>
            </Column>
             <Column field="state" header="State" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.state }}
                </template>
            </Column>
            <Column field="city" header="City" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.city }}
                </template>
            </Column>
            <Column field="address_line_1" header="Street Address" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.address_line_1 }}
                </template>
            </Column>
            <Column field="store_count" header="Store Count" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data?.stores_count }}
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
                            <button @click="requireConfirmation()" class="flex items-center gap-2 py-2 px-3 text-gray-700 hover:bg-red-100 hover:text-red-500 rounded-lg">
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
                                        <Button label="Cancel" outlined size="small" @click="acceptCallback"></Button>
                                        <Button label="Delete" severity="danger" size="small" @click="rejectCallback"></Button>
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
</template>
