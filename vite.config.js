import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css", 
                // "resources/js/app.js", 
                'resources/js/registration/student-store.js',
                'resources/js/registration/download-qr.js',
                'resources/js/student/delete-student.js',
                'resources/js/scan/scan.js',
                'resources/js/scan/scan2.js',
                'resources/js/scan/phonescan.js',
                // 'resources/js/delete-attendances.js',
                'resources/js/auth/login.js',
                'resources/js/instructor/delete.js',
                'resources/js/instructor/store.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
