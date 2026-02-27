import { defineConfig } from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'

export default defineConfig({
    server: {
        host: '192.168.1.3',
        port: 5173,
        cors: true
    },
    plugins: [
        symfonyPlugin(),
    ],
    build: {
        rollupOptions: {
            input: {
                app: './assets/app.js'
            }
        }
    } 
})
