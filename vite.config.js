import { defineConfig, loadEnv } from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'
import fs from 'fs'

const url = new URL(process.env.VITE_DEV_SERVER_URL || 'https://localhost:5173')

export default defineConfig(({ mode }) => {

    const env = loadEnv(mode, process.cwd(), '')

    const devUrl = env.VITE_DEV_SERVER_URL || 'https://localhost:5173'
    const url = new URL(devUrl)

    return {
        server: {
            host: url.hostname,
            port: Number(url.port) || 5173,
            cors: true,
            https: {
                key: fs.readFileSync('./cert/localhost.key'),
                cert: fs.readFileSync('./cert/localhost.crt')
            }
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
    }
})
