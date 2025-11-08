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
            <div class="hidden bg-cover lg:block lg:w-2/3" style="background-image: url(https://ncube.com/wp-content/uploads/2020/06/types-of-ecommerce.jpg)">
                <div class="flex items-center p-10">
                    <div>
                        <h2 v-animateonscroll="{ enterClass: 'animate-enter fade-up-10 slide-in-from-b-10 animate-duration-800', leaveClass: 'animate-leave fade-out-0' }" class="text-2xl font-bold text-white sm:text-3xl">Welcome to Tentomart Admin Area</h2>
                        <p v-animateonscroll="{ enterClass: 'animate-enter fade-up-10 slide-in-from-b-10 animate-duration-1000', leaveClass: 'animate-leave fade-out-0' }"  class="max-w-xl mt-3 text-gray-300">Tentomart is a Bangladeshi eCommerce SaaS platform that lets anyone create and manage their own online store.</p>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center w-full max-w-md px-6 mx-auto lg:w-2/6">
                <div v-animateonscroll="{ enterClass: 'animate-enter fade-in-10 zoom-in-75 animate-duration-1000' }" class="flex-1">
                    <div class="text-center">
                        <div class="flex justify-center mx-auto">
                            <img class="w-auto h-7 sm:h-10" src="@/assets/images/amar-ecommerce.png" alt="AmarEcommerce">
                        </div>
                        <p class="mt-3 text-gray-500 dark:text-gray-300">Sign in to access Dashboard</p>
                    </div>
    
                    <div class="mt-8">
                        <form @submit.prevent="submit">
                            <IftaLabel class="min-w-full mb-5">
                                <InputText class="w-full" id="email" v-model="form.email" autocomplete="off" :invalid="form.errors.email ? true : false" />
                                <label for="email">Email</label>
                                <Message v-if="form.errors.email" size="small" severity="error" variant="simple">{{ form.errors?.email }}</Message>
                            </IftaLabel>
            
                            <IftaLabel class="min-w-full">
                                <InputText class="w-full" id="password" v-model="form.password" autocomplete="off"  :invalid="form.errors.password ? true : false" />
                                <label for="password">Password</label>
                                <Message v-if="form.errors.password" size="small" severity="error" variant="simple">{{ form.errors?.password }}</Message>
                            </IftaLabel>
    
                            <div class="mt-6">
                                <BUtton type="submit" class="w-full">Sign in</BUtton>
                            </div>
                        </form>
                        <p class="mt-6 text-sm text-center text-gray-400">Don&#x27;t have an account yet? <a href="#" class="text-blue-500 focus:outline-none focus:underline hover:underline">Sign up</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
