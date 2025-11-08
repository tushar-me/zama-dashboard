<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import {ref, computed} from 'vue';
import { Category, Badge, Mockup } from '@/types';
import UFileUpload from '@/components/UFileUpload.vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    categories: {
        data: Category[]
    },
    badges: Badge[],
    mockup: Mockup,
    color_ids: any,
    colors: any,
    errors: any,
}>()

const isHtmlDescription = ref(false);
const toast = useToast();
const form = useForm({
    _method: 'PUT',
    name: props.mockup.name,
    description: props.mockup.description,
    image: null,
    measurement_guide: null,
    type: props.mockup.type,
    price: props.mockup.price,
    sell_price: props.mockup.sell_price,
    order_level: props.mockup.order_level,
    status: props.mockup.status,
    category_id: props.mockup.category_id,
    colors: props.color_ids,
    us_charge: props.mockup.shipping_charge?.us_charge,
    us_add_charge_per_item: props.mockup.shipping_charge?.us_add_charge_per_item,
    worldwide_charge: props.mockup.shipping_charge?.worldwide_charge,
    worldwide_add_charge_per_item: props.mockup.shipping_charge?.worldwide_add_charge_per_item,
    video_url: props.mockup.video_url,
    cost: props.mockup.cost,
    is_free: false,
     size_chart: props.mockup.size_chart?.chart_data
})
function addColumn() {
  const col = prompt("Enter new column name:");
  if (!col) return;
  
  form.size_chart.columns.push(col);
  form.size_chart.rows.forEach((r) => r.push(""));
}

function removeColumn(index: number) {
  form.size_chart.columns.splice(index, 1);
  form.size_chart.rows.forEach((r) => r.splice(index, 1));
}
function addRow() {
  const newEmptyRow = Array(form.size_chart.columns.length).fill("");
  form.size_chart.rows.push(newEmptyRow);
}

function removeRow(index: number) {
  form.size_chart.rows.splice(index, 1);
}

const onSubmit = () => {
    form.post(route('mockup.update', props.mockup.id), {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Mockup has been created successfully', life: 3000 });
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
</script>  
<template>
   <Head title="Create Edit" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white p-4">
        <form @submit.prevent="onSubmit">
            <h3 class="text-xl font-normal mb-4">Mockup Information</h3>
            <div class="flex gap-4 mb-4">
                <label for="category_id"  class="font-normal lg:w-1/4">Category</label>
                <div class="w-full lg:w-3/4">
                    <Select v-model="form.category_id" :options="categories.data" filter optionLabel="name" optionValue="id" placeholder="Select a Category" class="w-full lg:w-64" />
                    <Message v-if="errors.category_id" severity="error" size="small" variant="simple">{{ errors.category_id }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="name"  class="font-normal lg:w-1/4">Name</label>
                <div class="w-full lg:w-3/4">
                    <InputText v-model="form.name" id="name" :invalid="errors.name" autocomplete="off" />
                    <Message v-if="errors.name" severity="error" size="small" variant="simple">{{ errors.name }}</Message>
                </div>
            </div>
             <div class="flex gap-4 mb-4">
                <label for="price"  class="font-normal lg:w-1/4">Production Cost</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.cost" id="price" :invalid="errors.cost" autocomplete="off" />
                    <Message v-if="errors.cost" severity="error" size="small" variant="simple">{{ errors.cost }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="price"  class="font-normal lg:w-1/4">Price</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.price" id="price" :invalid="errors.price" autocomplete="off" />
                    <Message v-if="errors.price" severity="error" size="small" variant="simple">{{ errors.price }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="sell_price"  class="font-normal lg:w-1/4">Sell Price</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.sell_price" id="sell_price" :invalid="errors.sell_price" autocomplete="off" />
                    <Message v-if="errors.sell_price" severity="error" size="small" variant="simple">{{ errors.sell_price }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="type" class="font-normal lg:w-1/4">Type</label>
                <div class="w-full lg:w-3/4">
                    <div class="flex items-center gap-4"> 
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="form.type" inputId="default_print" name="type" value="default_print" />
                            <label for="default_print">Default Print</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton v-model="form.type" inputId="all_over_print" name="type" value="all_over_print" />
                            <label for="all_over_print">All Over Print</label>
                        </div>
                    </div>
                    <Message v-if="errors.type" severity="error" size="small" variant="simple">{{ errors.type }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-2">
                    <label for="imaage" class="font-normal lg:w-1/4">Image</label>
                   <div class="w-full lg:w-3/4">
                    <UFileUpload v-model="form.image" :initialImage="mockup.image" />
                    <span v-if="errors.image" class="text-red-500">{{ errors.image }}</span>
                   </div>
                </div>
            <div class="flex gap-4 mb-2">
                <label for="size_chart" class="font-normal lg:w-1/4">Measurement Guide</label>
                <div class="w-full lg:w-3/4">
                    <UFileUpload v-model="form.measurement_guide" :initialImage="mockup.measurement_guide" />
                    <span v-if="errors.measurement_guide" class="text-red-500">{{ errors.measurement_guide }}</span>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="video_url"  class="font-normal lg:w-1/4">Youtube Video Link</label>
                <div class="w-full lg:w-3/4">
                    <Textarea placeholder="https://youtube.com/watch..." v-model="form.video_url" :invalid="errors.video_url" class="w-full" />
                    <Message v-if="errors.video_url" severity="error" size="small" variant="simple">{{ errors.video_url }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="description"  class="font-normal lg:w-1/4">Description</label>
                <div class="w-full lg:w-3/4">
                    <Editor v-model="form.description" editorStyle="height: 320px" :invalid="errors.description" />
                    <div class="my-2">
                       <div class="mb-2">
                        <Button label="HTML" :severity="isHtmlDescription ? 'primary' : 'secondary'" @click="isHtmlDescription = !isHtmlDescription"/>
                       </div>
                        <Textarea autoResize v-if="isHtmlDescription" v-model="form.description" class="w-full" />
                    </div>
                    <Message v-if="errors.description" severity="error" size="small" variant="simple">{{ errors.description }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="size-chart"  class="font-normal lg:w-1/4">Size Chart</label>
                 <div class="w-full lg:w-3/4">
                     <div class="p-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between mb-3">
                            <h3 class="font-bold">Size Chart</h3>
                            <div class="flex gap-2">
                            <Button label="Add Column" icon="pi pi-plus" @click="addColumn" />
                            <Button label="Add Row" icon="pi pi-plus" severity="secondary" @click="addRow" />
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border-gray-300 bg-gray-100 rounded-lg shadow">
                                <thead>
                                    <tr>
                                        <th
                                            v-for="(col, ci) in form.size_chart?.columns"
                                            :key="ci"
                                            class="border border-gray-300 p-2 text-left"
                                        >
                                            {{ col }}
                                            <Button
                                                icon="pi pi-times"
                                                text
                                                size="small"
                                                severity="danger"
                                                @click="removeColumn(ci)"
                                            />
                                        </th>
                                        
                                        <th class="border border-gray-300 p-2">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(row, ri) in form.size_chart?.rows" :key="ri">
                                        
                                        <td
                                            v-for="(val, ci) in row" 
                                            :key="ci"
                                            class="border border-gray-300 p-2"
                                        >
                                            <InputText v-model="form.size_chart.rows[ri][ci]" class="w-full" />
                                        </td>

                                        <td class="border border-gray-300 p-2 text-center">
                                            <Button
                                                icon="pi pi-trash"
                                                text
                                                size="small"
                                                severity="danger"
                                                @click="removeRow(ri)"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="description"  class="font-normal lg:w-1/4">Colors</label>
                <div class="w-full lg:w-3/4">
                    <MultiSelect v-model="form.colors" display="chip" :options="colors.data" optionLabel="name" optionValue="id" filter placeholder="Select Color"
                         :maxSelectedLabels="5" class="w-full">
                    <template #option="slotProps">
                        <div class="flex items-center">
                            <span :style="{ backgroundColor: '#'+slotProps.option.hex_code }" class="size-6 rounded-full mr-2 block"></span>
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
                    <Message v-if="errors.colors" severity="error" size="small" variant="simple">{{ errors.colors }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="w-full lg:w-1/4">
                 </div>
                <Button type="submit" label="Save" icon="pi pi-save"  block />
            </div>
            <h3 class="text-xl font-normal mb-4">Shipping Information</h3>
            <div class="flex gap-4 mb-4">
                <label for="us_charge"  class="font-normal lg:w-1/4">US Charge</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.us_charge" id="us_charge" :invalid="errors.us_charge" autocomplete="off" />
                    <Message v-if="errors.us_charge" severity="error" size="small" variant="simple">{{ errors.us_charge }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="us_add_charge_per_item"  class="font-normal lg:w-1/4">US Add Charge Per Item</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.us_add_charge_per_item" id="us_add_charge_per_item" :invalid="errors.us_add_charge_per_item" autocomplete="off" />
                    <Message v-if="errors.us_add_charge_per_item" severity="error" size="small" variant="simple">{{ errors.us_add_charge_per_item }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="worldwide_charge"  class="font-normal lg:w-1/4">Worldwide Charge</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.worldwide_charge" id="worldwide_charge" :invalid="errors.worldwide_charge" autocomplete="off" />
                    <Message v-if="errors.worldwide_charge" severity="error" size="small" variant="simple">{{ errors.worldwide_charge }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <label for="worldwide_add_charge_per_item"  class="font-normal lg:w-1/4">Worldwide Add Charge Per Item</label>
                <div class="w-full lg:w-3/4">
                    <InputNumber currency="USD" mode="currency" v-model="form.worldwide_add_charge_per_item" id="worldwide_add_charge_per_item" :invalid="errors.worldwide_add_charge_per_item" autocomplete="off" />
                    <Message v-if="errors.worldwide_add_charge_per_item" severity="error" size="small" variant="simple">{{ errors.worldwide_add_charge_per_item }}</Message>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="w-full lg:w-1/4">
                 </div>
                <Button type="submit" label="Save With Shipping" icon="pi pi-save" />
            </div>
        </form>
    </div>
</template>
