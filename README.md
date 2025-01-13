
# Insight - Jacked RAG

RAG - Retrieval Augmented Generation. This is the Jacked initiative for that within the PHP ecosystem.

## Usage

Sample credentials:

Username: `jekyll@example.com`

Password: `password`

![Insight Example](./public/insight.gif)

## Installation

Before start you need to make sure you have the following dependencies:

- php8.2 or higher
- php openswoole extension
- docker

### Step 1

Clone the repository:

```bash
git clone https://github.com/Jacked-PHP/insight my-project
```

Then `cd my-project` 

### Step 2

Install dependencies (php composer and npm):

```bash
composer install
```

```bash
npm install
```

### Step 3

```bash
npm run build
```
(or `npm run dev` if you are developing and want the hot reload)

### Step 4

Prepare the database:

```bash
docker-compose up -d
```

### Step 5

Make sure your `.env` is properly set:

- have db credentials
- app key (`php artisan key:generate`)
- app url (default is `http://localhost:8000`)

Then run the migrations:

```bash
php artisan migrate:fresh --seed
```

Run the server (this is running jacked server):

```bash
docker compose up -d
```

Now you can visit `http://localhost:8080` and see the application running.

## Vector DB & Embedding Solution

There are a few options for vector DB. This project's sample version starts with a Filesystem solution. It is recommended to visit https://github.com/LLPhant/LLPhant for more information since the llm solution is built on top of that.

The recommended models for embedding are:

- [mxbai-embed-large](https://ollama.com/library/mxbai-embed-large)
- [nomic-embed-text](https://ollama.com/library/nomic-embed-text)

## Ollama details

To run Ollama, a suggested way would be to run through docker:

```shell
# Consider network parameter if you want to connect to the same network as the php service.
docker run -d -v ollama:$HOME/.ollama -p 11434:11434 --name ollama ollama/ollama
```

> Reference: https://github.com/ollama/ollama/blob/main/docs/docker.md

## Extra

This project also runs on NativePHP. 
