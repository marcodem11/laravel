<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Profile" />

    <div class="py-6">
      <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

        <div class="card">
          <div class="card-body">
            <UpdateProfileInformationForm />
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <UpdatePasswordForm />
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <DeleteUserForm />
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>