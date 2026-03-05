export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  ssr: false,
  devtools: { enabled: false },

  modules: [
    '@pinia/nuxt',
    '@nuxt/ui',
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000',
      appName: 'Teranga Business Hub',
    },
  },

  app: {
    head: {
      title: 'Teranga Business Hub',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Plateforme fintech de finance solidaire - Teranga Business Hub' },
      ],
    },
  },

  css: ['~/assets/css/main.css'],

  typescript: {
    strict: true,
  },

  experimental: {
    payloadExtraction: false,
  },
})
