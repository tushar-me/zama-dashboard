<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
})
const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Welcome"></Head>
    <div class="bg-white dark:bg-gray-900">
        <div class="flex justify-center h-screen">
            <div class="flex items-center w-full max-w-md px-6 mx-auto lg:w-2/6">
                <div v-animateonscroll="{ enterClass: 'animate-enter fade-in-10 zoom-in-75 animate-duration-1000' }" class="flex-1">
                    <div class="text-center">
                        <div class="flex justify-center mx-auto">
                            <img class="w-auto h-7 sm:h-10" src="" alt="AmarEcommerce">
                        </div>
                    </div>
    
                    <div class="mt-8">
                        <form @submit.prevent="submit" class="space-y-6">
                            <FloatLabel class="min-w-full">
                                <InputText fluid id="email" v-model="form.email" :invalid="form.errors.email ? true : false" />
                                <label for="email">Email</label>
                                <Message v-if="form.errors.email" size="small" severity="error" variant="simple">{{ form.errors?.email }}</Message>
                            </FloatLabel>
                            <FloatLabel class="w-full">
                                <Password fluid id="password" toggleMask :feedback="false"  v-model="form.password" :invalid="form.errors.password ? true : false" />
                                <label for="password">Password</label>
                                <Message v-if="form.errors.password" size="small" severity="error" variant="simple">{{ form.errors?.password }}</Message>
                            </FloatLabel>
    
                            <div class="mt-6">
                                <BUtton icon="pi pi-sign-in" type="submit" class="w-full" label="Login" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
