# 🔒 Toolbox de Sécurité

## Introduction

La Toolbox de Sécurité est un ensemble complet d'outils d'analyse de sécurité conçu pour vous assister dans la réalisation de divers audits de sécurité. Elle inclut des fonctionnalités telles que les tests réseau (ping, traceroute), l'analyse de ports avec Nmap, les scans de vulnérabilités web avec Nikto, l'énumération de répertoires avec Dirb, ainsi que des scans spécifiques utilisant des outils comme Whois, Dig, SSLScan, Nuclei, Subfinder et WhatWeb.

## Fonctionnalités clés

- 👀 **Reconnaissance**: Exploration des systèmes cibles pour collecter des informations initiales sur les cibles potentielles.

- 🔍 **Scanning**: Analyse des systèmes pour identifier les ports ouverts, les services en cours d'exécution et les vulnérabilités connues.

- 💪 **Exploitation**: Utilisation des vulnérabilités détectées pour accéder aux systèmes cibles et obtenir un accès non autorisé.

- 📄 **Reporting**: Génération de rapports détaillés pour documenter les résultats des tests de pénétration.

## Fonctionnalités avancées
 
- **Tests Réseau**
  - **Ping** : Vérifie si une cible est accessible sur le réseau.
  - **Traceroute** : Trace le chemin des paquets vers une cible spécifique.

- **Énumération et Analyse**
  - **Nmap** : Réalise un scan complet des ports et détecte les services avec leurs versions.
  - **Nikto** : Scanne les serveurs web pour détecter des vulnérabilités connues.

- **Outils Web**
  - **Dirb** : Découvre des répertoires et fichiers cachés sur les serveurs web.
  - **WebCheck** : Fournit une analyse complète d'un site web via l'API web-check.xyz.

- **Recherches d'Information**
  - **Whois** : Obtient des informations sur le propriétaire d'un domaine.
  - **Dig** : Interroge les serveurs DNS pour récupérer des informations sur les domaines.

- **Outils de Sécurité Avancés**
  - **SSLScan** : Évalue la configuration SSL/TLS d'un serveur.
  - **Nuclei** : Scanne pour des vulnérabilités spécifiques en utilisant des modèles personnalisables.
  - **Subfinder** : Découvre des sous-domaines associés à un domaine principal.
  - **WhatWeb** : Identifie les technologies utilisées par un site web.

## Prérequis

- PHP 7.4
- Serveur web (Apache, Nginx)
- Accès en ligne de commande aux outils suivants :
  - ping, traceroute
  - nmap
  - nikto
  - dirb
  - curl
  - whois
  - dig
  - sslscan
  - nuclei
  - subfinder
  - whatweb

## Installation

1. Clonez ce dépôt sur votre serveur web :
   ```
   git clone https://github.com/Jibolartieur/toolbox-sdv//toolbox-sdv.git
   ```

## Sécurité

Cette toolbox est conçue pour des tests de sécurité légitimes. Assurez-vous de :

- Utiliser ces outils uniquement sur des systèmes pour lesquels vous avez l'autorisation de les tester
- Limiter l'accès à l'API aux utilisateurs autorisés
- Surveiller l'utilisation de l'API pour détecter tout abus potentiel

## Licence

Ce projet est sous licence MIT. Voir le fichier License pour plus d'informations. 
