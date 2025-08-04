import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/diogogpinto/filament-auth-ui-enhancer/resources/**/*.blade.php",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                custom: ["Plus Jakarta Sans", "sans-serif"],
            },
            screens: {
                // range 0px - 640px
                android: "640px",
                // range 640px - 1280px
                laptop: "1280px",
                // range 1280px - 1920px
                desktop: "1920px",
            },
            colors: {
                // putih
                primary: "#F1F1F1",
                // agak biru
                secondary: "var(--color-secondary)",
                typography: {
                    // agak biru
                    main: "#31487A",
                    dark: "#2C2C2C",
                    hover1: "#223255",
                    hover2: "#909090",
                },
            },
            borderRadius: {
                "xl-figma": "30px", // untuk 30px rounded
                "lg-figma": "15px", // untuk 15px rounded
            },
            fontSize: {
                h1: ["56px", { lineHeight: "1.5", fontWeight: "400" }],
                h2: ["48px", { lineHeight: "1.5", fontWeight: "400" }],
                h3: ["46px", { lineHeight: "1.5", fontWeight: "400" }],
                h4: ["24px", { lineHeight: "1.5", fontWeight: "400" }],
                h5: ["18px", { lineHeight: "1.5", fontWeight: "400" }],
                h6: ["15px", { lineHeight: "1.5", fontWeight: "400" }],
                "h1-bold": ["56px", { lineHeight: "1.1", fontWeight: "700" }],
                "h2-bold": ["48px", { lineHeight: "1.1", fontWeight: "700" }],
                "h3-bold": ["46px", { lineHeight: "1.1", fontWeight: "700" }],
                "h4-bold": ["24px", { lineHeight: "1.1", fontWeight: "700" }],
                "h5-bold": ["18px", { lineHeight: "1.1", fontWeight: "700" }],
                "h6-bold": ["15px", { lineHeight: "1.1", fontWeight: "700" }],
                body: ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                "body-bold": ["16px", { lineHeight: "1.1", fontWeight: "700" }],
            },
        },
    },

    plugins: [forms, typography],
};
