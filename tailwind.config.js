module.exports = {
  content: [
    // https://tailwindcss.com/docs/content-configuration
    "./*.php",
    "./**/*.php",
  ],
  theme: {
    container: {
      center: true, // Mantiene el contenedor centrado
      padding: {
        DEFAULT: "1rem",
        sm: "2rem",
        lg: "4rem",
        xl: "5rem",
        "2xl": "6rem",
      },
      screens: {
        sm: "640px",
        md: "768px",
        lg: "1024px",
        xl: "1280px",
        "2xl": "80%", // Aquí cambiamos a porcentaje
      },
    },
    extend: {
      colors: {
        orange: "#9A7A14",
        "custom-grey": "#585858",
        rojo: "#D22800",
        gris: "#47586A",
        swhite: "#F5F5ED",
        gold: "#D8D4AE",
        darkgold: "#9A7A14",
        lightgrey: "#C2C2C2",
        rosado: "#EA6060",
        selection: "#04A4CC",
      },
      fontFamily: {
        alegreya: ["Alegreya", "serif"],
        sans: ["Alegreya", "system-ui", "sans-serif"],
      },
      typography: (theme) => ({
        DEFAULT: {
          css: {
            maxWidth: "none",
            color: "#000",
            "::selection": {
              backgroundColor: theme("colors.selection"),
              color: "#fff",
            },
            p: {
              marginTop: "1.5em",
              marginBottom: "1.5em",

              lineHeight: "1.5",
              fontWeight: "400",
              "::selection": {
                backgroundColor: theme("colors.selection"),
                color: "#fff",
              },
            },
            h1: {
              color: theme("colors.rojo"),
              fontWeight: "500",
            },
            h2: {
              color: theme("colors.rojo"),
              fontWeight: "400",
            },
            h3: {
              color: theme("colors.rojo"),
              fontWeight: "500",
            },
            h4: {
              fontWeight: "500",
            },
            a: {
              color: theme("colors.rosado"),
              textDecoration: "underline",
              "&:hover": {
                color: theme("colors.darkgold"),
              },
            },
            "a:has(sup)": {
              textDecoration: "none",
            },
            sup: {
              fontSize: [
                "1.2rem",
                {
                  md: "1.5rem", // Tablet
                  lg: "1.7rem", // Desktop
                },
              ],
              verticalAlign: "baseline",
              position: "relative",
              bottom: "0.1em",
              top: "-0.3rem",
              marginLeft: "0.1em",
              a: {
                color: theme("colors.rojo"),
                textDecoration: "none",
                "&:hover": {
                  color: theme("colors.gold"),
                },
              },
            },
            figcaption: {
              color: "#000",
            },
            blockquote: {
              borderLeftWidth: "0",
              borderLeftColor: "transparent",
              paddingLeft: "2em",
              fontStyle: "italic",
              marginLeft: 0,
              marginRight: 0,
            },
            img: {
              marginTop: "2em",
              marginBottom: "2em",
            },
            ul: {
              listStyleType: "disc",
              marginTop: "1.5em",
              marginBottom: "1.5em",
            },
            ol: {
              listStyleType: "decimal",
              marginTop: "1.5em",
              marginBottom: "1.5em",
            },
            "ul, ol": {
              paddingLeft: "1.5em",
            },
            li: {
              marginTop: "0.5em",
              marginBottom: "0.5em",
            },
            "p.has-small-font-size": {
              fontSize: "2rem",
            },
            ".wp-block-separator": {
              borderTop: "2px solid",
              borderColor: "#000",
              borderBottom: "none",
              margin: "2em 0",
            },
          },
        },
      }),
    },
  },
  plugins: [require("@tailwindcss/typography")],
};
