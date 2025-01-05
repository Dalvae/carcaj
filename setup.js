const { execSync } = require("child_process");
const fs = require("fs");
const path = require("path");
const tar = require("tar");
const CONFIG = {
  backupDir: "./backup",
  tempDir: "./temp",
  envFile: "./.env",
};

// Función para extraer valores de wp-config.php
function getConfigValue(content, key) {
  const regex = new RegExp(`define\\(['"]${key}['"],\\s*['"](.+?)['"]\\)`);
  const match = content.match(regex);
  return match ? match[1] : null;
}

// Función para actualizar valores en wp-config.php
function updateConfigValue(content, key, value) {
  // Si el valor es una cadena, lo envolvemos en comillas
  const formattedValue = typeof value === "string" ? `'${value}'` : value;

  // Primero intentamos buscar si ya existe la definición
  const existingDefineRegex = new RegExp(
    `define\\(['"]${key}['"],\\s*(.+?)\\);`
  );
  if (content.match(existingDefineRegex)) {
    // Si existe, la actualizamos
    return content.replace(
      existingDefineRegex,
      `define('${key}', ${formattedValue});`
    );
  }
  // Si no existe, la añadimos antes del cierre
  return content.replace(
    "/* That's all, stop editing! Happy publishing. */",
    `define('${key}', ${formattedValue});\n/* That's all, stop editing! Happy publishing. */`
  );
}

async function setup() {
  try {
    console.log("🚀 Iniciando setup del entorno de desarrollo...");
    // Detener y limpiar contenedores Docker existentes
    console.log("🔄 Deteniendo y limpiando contenedores Docker existentes...");
    try {
      execSync("docker-compose down -v", { stdio: "inherit" });
    } catch (error) {
      console.log("ℹ️  No hay contenedores previos para detener");
    }

    // Verificar backup
    if (!fs.existsSync(CONFIG.backupDir)) {
      throw new Error(`No se encontró el directorio ${CONFIG.backupDir}`);
    }

    const backupFile = fs
      .readdirSync(CONFIG.backupDir)
      .find((file) => file.endsWith(".tar.gz"));
    if (!backupFile) {
      throw new Error(
        "No se encontró archivo de backup .tar.gz en la carpeta backup/"
      );
    }

    const backupDirName = path.basename(backupFile, ".tar.gz");
    const backupDir = path.join(CONFIG.tempDir, backupDirName);

    // Extraer backup si es necesario
    if (!fs.existsSync(backupDir)) {
      console.log("📦 Extrayendo backup...");
      if (!fs.existsSync(CONFIG.tempDir)) {
        fs.mkdirSync(CONFIG.tempDir);
      }
      await tar.x({
        file: path.join(CONFIG.backupDir, backupFile),
        cwd: CONFIG.tempDir,
      });
      console.log(`📂 Backup extraído en: ${backupDir}`);
    }

    // Leer wp-config.php para obtener la configuración de la base de datos
    const wpConfigPath = path.join(
      backupDir,
      "homedir/public_html/wp-config.php"
    );
    let wpConfigContent = fs.readFileSync(wpConfigPath, "utf8");

    // Extraer valores de configuración
    const dbConfig = {
      name: getConfigValue(wpConfigContent, "DB_NAME"),
      user: getConfigValue(wpConfigContent, "DB_USER"),
      password: getConfigValue(wpConfigContent, "DB_PASSWORD"),
    };

    // Crear directorio para archivos temporales si no existe
    const configTempDir = path.join(CONFIG.tempDir, "config");
    if (!fs.existsSync(configTempDir)) {
      fs.mkdirSync(configTempDir, { recursive: true });
    }

    // Modificar valores en wp-config.php
    console.log("📝 Actualizando wp-config.php para desarrollo local");
    wpConfigContent = updateConfigValue(wpConfigContent, "DB_HOST", "db");
    wpConfigContent = updateConfigValue(wpConfigContent, "WP_DEBUG", false); // sin comillas porque es booleano
    wpConfigContent = updateConfigValue(
      wpConfigContent,
      "IS_VITE_DEVELOPMENT",
      true
    ); // sin comillas porque es booleano

    // Agregar configuraciones útiles para desarrollo
    wpConfigContent = wpConfigContent.replace(
      "/* That's all, stop editing! Happy publishing. */",
      `
    /* Configuraciones adicionales para desarrollo local */
    define('WP_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', true);
    define('SCRIPT_DEBUG', true);
    define('CONCATENATE_SCRIPTS', false);
    define('IS_VITE_DEVELOPMENT', true); 
    /* That's all, stop editing! Happy publishing. */`
    );
    // Guardar wp-config.php modificado
    const modifiedWpConfigPath = path.join(configTempDir, "wp-config.php");
    fs.writeFileSync(modifiedWpConfigPath, wpConfigContent);
    console.log(
      `✅ wp-config.php modificado guardado en: ${modifiedWpConfigPath}`
    );

    // Buscar archivo SQL en el backup
    const absolutePaths = {
      mysql: path.join(backupDir, "mysql"),
    };
    const sqlFile = fs
      .readdirSync(absolutePaths.mysql)
      .find((file) => file.endsWith(".sql"));
    if (!sqlFile) {
      throw new Error("No se encontró archivo SQL en el backup");
    }
    const sqlFilePath = path.join(absolutePaths.mysql, sqlFile);
    console.log(`💾 Encontrado archivo SQL: ${sqlFile}`);

    // Generar contenido del .env
    const envContent = `
# Rutas
WP_ROOT=${path.resolve(backupDir, "homedir/public_html")}
THEME_DIR=${path.resolve(process.cwd())}
WP_CONFIG=${path.resolve(modifiedWpConfigPath)}
SQL_FILE=${path.resolve(sqlFilePath)}
# Base de datos
DB_NAME=${dbConfig.name}
DB_USER=${dbConfig.user}
DB_PASSWORD=${dbConfig.password}
DB_HOST=db
DB_PREFIX=wpyl_
# WordPress
WP_DEBUG=true
WP_PORT=8888
IS_VITE_DEVELOPMENT=true 
# Docker
COMPOSE_PROJECT_NAME=carcaj
`.trim();

    // Guardar .env
    fs.writeFileSync(CONFIG.envFile, envContent);
    console.log("📝 Archivo .env generado con la configuración");

    // Iniciar contenedores Docker
    console.log("\n🐳 Iniciando contenedores Docker...");
    try {
      execSync("docker-compose up -d", { stdio: "inherit" });
      console.log("✅ Contenedores Docker iniciados correctamente");
    } catch (error) {
      throw new Error("Error al iniciar contenedores Docker: " + error.message);
    }

    console.log("\n✅ Setup completado!");
    console.log("\nPara continuar con el desarrollo:");
    console.log("1. Ejecuta: pnpm dev");
    console.log("2. Visita: http://localhost:8888");
  } catch (error) {
    console.error("❌ Error durante el setup:", error.message);
    process.exit(1);
  }
}

setup();

setup();
