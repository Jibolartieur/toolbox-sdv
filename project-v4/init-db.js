import mysql from 'mysql2/promise';
import bcrypt from 'bcryptjs';
import dotenv from 'dotenv';

dotenv.config();

const dbConfig = {
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
};

async function initializeDatabase() {
  try {
    // Créer la connexion
    const connection = await mysql.createConnection(dbConfig);

    // Créer la base de données
    await connection.query(`CREATE DATABASE IF NOT EXISTS ${process.env.DB_NAME}`);
    await connection.query(`USE ${process.env.DB_NAME}`);

    // Créer la table users
    await connection.query(`
      CREATE TABLE IF NOT EXISTS users (
        id VARCHAR(36) PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // Créer la table scan_results
    await connection.query(`
      CREATE TABLE IF NOT EXISTS scan_results (
        id VARCHAR(36) PRIMARY KEY,
        tool VARCHAR(50) NOT NULL,
        target VARCHAR(255) NOT NULL,
        output TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id VARCHAR(36) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
      )
    `);

    // Créer l'utilisateur admin par défaut
    const adminPassword = await bcrypt.hash('admin', 10);
    await connection.query(`
      INSERT IGNORE INTO users (id, email, password)
      VALUES (UUID(), 'admin', ?)
    `, [adminPassword]);

    console.log('Base de données initialisée avec succès');
    await connection.end();
  } catch (error) {
    console.error('Erreur lors de l\'initialisation de la base de données:', error);
    process.exit(1);
  }
}

initializeDatabase();