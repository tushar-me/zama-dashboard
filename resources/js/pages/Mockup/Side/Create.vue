<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import {ref, computed, watch} from 'vue';
import { Mockup } from '@/types';
import { FileUploadSelectEvent } from 'primevue/fileupload';
import DraggableResizableVue from 'draggable-resizable-vue3';

defineOptions({ layout: AppLayout });

const home = ref({
    label: 'Dashboard',
    icon: 'pi pi-home',

});
const items = ref([
    { label: 'Mockup', to: '/mockup'},
    { label: 'Side', to: '/mockup-side'},
    { label: 'Create'}
]);
const props = defineProps<{
    mockup: Mockup,
    errors: any,
}>()

const toast = useToast();
const e = ref();
const onFileSelect = (event: FileUploadSelectEvent, index: number) => {
    e.value = event;
    const file = event.files[0];
    form.sides[index].image = file;
    form.sides[index].peviewImg = URL.createObjectURL(file);
}
const onClear = (index: number) => {
    form.sides[index].image = null;
    form.sides[index].peviewImg = '';
}
const form = useForm({
    mockup_id: props.mockup?.id || '',
    sides: [{
        name: '',
        image: null,
        peviewImg: '',
        order_level: 1,
        bounding_box: {
            top: 100,
            left: 170,
            width: 150,
            height: 150,
            css: '',
            border_color: '000000',
            border_width: 1,
            background_color: 'ffffff',
        },
        status: true,
    }],
})

const addSide = () => {
    form.sides.push({
        name: '',
        image: null,
        peviewImg: '',
        bounding_box: {
            top: 100,
            left: 170,
            width: 150,
            height: 150,
            css: '',
            border_color: 'ff0000',
            border_width: 1,
            background_color: 'ffffff',
        },
        status: true,
        order_level: form.sides.length + 1,
    });
}
const removeSide = (index: number) => {
    form.sides.splice(index, 1);
}
const onSubmit = () => {
    form.post(route('mockup-side.store'), {
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
    <Head title="Create Mockup" />
    <Toast />
    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
        <Breadcrumb :home="home" :model="items" />
        <div class="p-10 pt-5">
            <form @submit.prevent="onSubmit">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-normal">Mockup Side Information</h3>
                    <Button :disabled="form.processing" type="submit" label="Save Changes" icon="pi pi-save" />
                </div>
                <div v-for="(side, index) in form.sides" :key="index" class="mb-4 flex flex-wrap border-b border-gray-300 pb-5">
                    <div class="w-full lg:w-1/2">
                        <div class="flex gap-4 mb-4">
                            <label for="name"  class="font-normal lg:w-1/4">Name</label>
                            <div class="w-full lg:w-3/4">
                                <InputText v-model="side.name" id="name" :invalid="errors[`sides.${index}.name`]" autocomplete="off" />
                                <Message
                                    v-if="errors[`sides.${index}.name`]"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                    >
                                    {{ errors[`sides.${index}.name`] }}
                                </Message>
                            </div>
                        </div>
                        <div class="flex gap-4 mb-2">
                            <label for="imaage" class="font-normal lg:w-1/4">Image</label>
                            <div class="w-full lg:w-3/4 flex gap-5">
                                <div>
                                    <FileUpload :multiple="false" chooseIcon="pi pi-cloud-upload" :showUploadButton="false" @clear="onClear(index)" :auto="true" @select="onFileSelect( $event, index)"  accept="image/*">
                                        <template #content>
                                            <div 
                                            v-if="side.peviewImg" 
                                            id="parent" 
                                            class="relative size-[500px]"
                                                :style="
                                                    {
                                                        backgroundImage: `url(${side.peviewImg})`,
                                                        backgroundSize: 'cover',
                                                        backgroundPosition: 'center',
                                                        backgroundRepeat: 'no-repeat',
                                                        backgroundColor: `#${side.bounding_box.background_color}`,
                                                    }
                                                "
                                            >
                                                <DraggableResizableVue 
                                                v-model:x="side.bounding_box.left"
                                                v-model:y="side.bounding_box.top"
                                                v-model:h="side.bounding_box.height"
                                                v-model:w="side.bounding_box.width"
                                                class="p-1 cursor-move" parent="#parent">
                                                    <span class="block w-full h-full" :style="
                                                        {
                                                            border: `${side.bounding_box.border_width}px solid #${side.bounding_box.border_color}`,
                                                        }
                                                    "></span>
                                                </DraggableResizableVue>
                                            </div>
                                            <div v-else class="flex flex-col items-center justify-center size-96 border border-gray-300 rounded-lg">
                                                <Icon name="line-md:uploading-loop" class="text-5xl text-gray-400" />
                                                <span class="text-gray-500">Drag and drop files to here to upload.</span>
                                            </div>
                                        </template>
                                    </FileUpload>
                                    <Message
                                        v-if="errors[`sides.${index}.image`]"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                        >
                                        {{ errors[`sides.${index}.image`] }}
                                    </Message>
                                    <div v-if="side.peviewImg">
                                        <h3 class="text-sm font-normal py-2">Background</h3>
                                        <ul class="flex flex-wrap">
                                            <li v-for="color in mockup?.colors">
                                                <input class="peer hidden" name="background-color" :id="`background-color-${index}-${color.id}`" :value="color.hex_code" type="radio" v-model="side.bounding_box.background_color" />
                                                <label :for="`background-color-${index}-${color?.id}`" class="block border border-transparent peer-checked:border-black p-0.5 rounded-lg">
                                                    <span :style="{ backgroundColor: '#'+color.hex_code }" class="size-6 rounded-md block"></span>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5 border border-gray-200 rounded-lg p-4 bg-stone-100" v-if="side.peviewImg">
                                    <div class="space-y-4">
                                        <label for="bounding_box_top" class="font-normal text-sm">Top</label>
                                        <InputNumber showButtons  v-model="side.bounding_box.top" id="bounding_box_top" :invalid="errors.bounding_box_top" autocomplete="off" />
                                        <Slider v-model="side.bounding_box.top" class="w-full" :min="0" :max="300" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_left" class="font-normal text-sm">Left</label>
                                        <InputNumber showButtons  v-model="side.bounding_box.left" id="bounding_box_left" :invalid="errors.bounding_box_left" autocomplete="off" />
                                        <Slider v-model="side.bounding_box.left" class="w-full" :min="0" :max="300" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_width" class="font-normal text-sm">Width</label>
                                        <InputNumber showButtons  v-model="side.bounding_box.width" id="bounding_box_width" :invalid="errors.bounding_box_width" autocomplete="off" />
                                        <Slider v-model="side.bounding_box.width" class="w-full" :min="0" :max="300" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_left" class="font-normal text-sm">Height</label>
                                        <InputNumber showButtons  v-model="side.bounding_box.height" id="bounding_box_height" :invalid="errors.bounding_box_height" autocomplete="off" />
                                        <Slider v-model="side.bounding_box.height" class="w-full" :min="0" :max="300" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_border_color" class="font-normal text-sm">Border Color</label>
                                        <ColorPicker inline  v-model="side.bounding_box.border_color" id="bounding_box_border_color" :invalid="errors.bounding_box_border_color" autocomplete="off" />
                                    </div>
                                    <div class="space-y-4">
                                        <label for="bounding_box_border_width" class="font-normal text-sm">Border Width</label>
                                        <InputNumber :min="1" :max="5" showButtons  v-model="side.bounding_box.border_width" id="bounding_box_border_width" :invalid="errors.bounding_box_border_width" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.sides.length > 1">
                            <Button @click="removeSide(index)" type="button" severity="danger" label="Remove" icon="pi pi-times" />
                        </div>
                    </div>
                </div>

                <div>
                    <div class="w-full lg:w-1/4">
                    </div>
                    <Button v-if="form.sides.length < 2" @click="addSide" type="button" label="Add Side" icon="pi pi-plus"  block />
                </div>
            </form>
        </div>
    </div>
</template>
