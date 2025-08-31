<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Flash from '@/Components/Flash.vue';
import { Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
const showingNavigationDropdown = ref(false);
</script>

<template>
  <div class="min-h-screen bg-neutral-900 text-neutral-100">
    <!-- Topbar -->
    <nav class="bg-neutral-950/95 border-b border-neutral-800 backdrop-blur">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex">
            <!-- Logo -->
            <div class="flex shrink-0 items-center">
              <Link :href="route('dashboard')" class="inline-flex items-center gap-2">
                <ApplicationLogo class="block h-7 w-auto fill-current text-neutral-200" />
                <span class="hidden sm:block text-sm font-medium text-neutral-300">Dashboard</span>
              </Link>
            </div>

            <!-- Nav links -->
            <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:items-center sm:gap-1">
              <NavLink
                :href="route('dashboard')"
                :active="route().current('dashboard')"
                class="text-neutral-300 data-[active=true]:text-white data-[active=true]:bg-neutral-800"
              >
                Dashboard
              </NavLink>

              <!-- User area -->
              <NavLink
                :href="route('requests.mine')"
                :active="route().current('requests.mine')"
                class="text-neutral-300 data-[active=true]:text-white data-[active=true]:bg-neutral-800"
              >
                Le mie richieste
              </NavLink>
              <NavLink
                :href="route('reservations.mine')"
                :active="route().current('reservations.mine')"
                class="text-neutral-300 data-[active=true]:text-white data-[active=true]:bg-neutral-800"
              >
                Le mie prenotazioni
              </NavLink>
              <NavLink
                :href="route('requests.create')"
                :active="route().current('requests.create')"
                class="text-neutral-300 data-[active=true]:text-white data-[active=true]:bg-neutral-800"
              >
                Nuova richiesta
              </NavLink>

              <!-- Admin -->
              <template v-if="user?.role === 'admin'">
                <NavLink href="/admin/items" :active="route().current('items.index')" class="text-neutral-300 data-[active=true]:text-white data-[active=true]:bg-neutral-800">
                  Admin • Inventario
                </NavLink>
                <NavLink href="/admin/requests" :active="route().current('admin.requests.index')" class="text-neutral-300 data-[active=true]:text-white data-[active=true]:bg-neutral-800">
                  Admin • Richieste
                </NavLink>
              </template>
            </div>
          </div>

          <!-- User dropdown -->
          <div class="hidden sm:ms-6 sm:flex sm:items-center">
            <div class="relative ms-3">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <span class="inline-flex rounded-md">
                    <button
                      type="button"
                      class="inline-flex items-center rounded-md border border-neutral-700 bg-neutral-900 px-3 py-2 text-sm font-medium text-neutral-300 transition hover:text-white focus:outline-none focus:ring-2 focus:ring-neutral-600"
                    >
                      {{ user.name }}
                      <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                  </span>
                </template>

                <template #content>
                  <DropdownLink :href="route('profile.edit')" class="text-neutral-800">
                    Profile
                  </DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button" class="text-neutral-800">
                    Log Out
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>

          <!-- Hamburger -->
          <div class="-me-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center rounded-md p-2 text-neutral-400 transition hover:bg-neutral-800 hover:text-neutral-200 focus:bg-neutral-800 focus:text-neutral-200 focus:outline-none"
            >
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path
                  :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden border-t border-neutral-800">
        <div class="space-y-1 pb-3 pt-2">
          <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" class="text-neutral-200">
            Dashboard
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('requests.mine')" :active="route().current('requests.mine')" class="text-neutral-200">
            Le mie richieste
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('reservations.mine')" :active="route().current('reservations.mine')" class="text-neutral-200">
            Le mie prenotazioni
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('requests.create')" :active="route().current('requests.create')" class="text-neutral-200">
            Nuova richiesta
          </ResponsiveNavLink>

          <template v-if="user?.role === 'admin'">
            <ResponsiveNavLink href="/admin/items" :active="route().current('items.index')" class="text-neutral-200">
              Admin • Inventario
            </ResponsiveNavLink>
            <ResponsiveNavLink href="/admin/requests" :active="route().current('admin.requests.index')" class="text-neutral-200">
              Admin • Richieste
            </ResponsiveNavLink>
          </template>
        </div>

        <!-- Mobile user / logout -->
        <div class="border-t border-neutral-800 pb-1 pt-4">
          <div class="px-4">
            <div class="text-base font-medium text-neutral-100">{{ user.name }}</div>
            <div class="text-sm font-medium text-neutral-400">{{ user.email }}</div>
          </div>
          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.edit')" class="text-neutral-200">
              Profile
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-neutral-200">
              Log Out
            </ResponsiveNavLink>
          </div>
        </div>
      </div>

      <!-- Flash toasts -->
      <Flash :flash="$page.props.flash" />
    </nav>

    <!-- Header slot -->
    <header v-if="$slots.header" class="bg-neutral-900 border-b border-neutral-800">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Main -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>