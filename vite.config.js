import { defineConfig, loadEnv } from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'
import tailwindcss from '@tailwindcss/vite'
import fs from 'fs'

export default defineConfig(({ mode }) => {

    const env = loadEnv(mode, process.cwd(), '')

    const devUrlServer = env.VITE_DEV_SERVER_URL || 'http://localhost:5173'
    const devUrlAnonce = env.VITE_DEV_ANNONCE_URL || 'http://localhost:5173'

    const url = new URL(devUrlServer)

    const isHttps = url.protocol === 'https:'

    return {
        server: {
            host: url.hostname,
            port: Number(url.port) || 5173,
            cors: true,

            // Active HTTPS seulement si l'URL commence par https
            https: isHttps
                ? {
                    key: fs.readFileSync('./cert/localhost.key'),
                    cert: fs.readFileSync('./cert/localhost.crt')
                }
                : false,

            // URL envoyée au navigateur
            origin: devUrlAnonce,
        },

        plugins: [
            symfonyPlugin(),
            tailwindcss()
        ],

        build: {
            rollupOptions: {
                input: {
                    app: './assets/app.js'
                }
            }
        }
    }
})
