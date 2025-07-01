
DEV_COMPOSE=compose.dev.yaml
PROD_COMPOSE=compose.prod.yaml

.PHONY: up-dev down-dev up-prod down-prod

up-dev:
	docker compose -f $(DEV_COMPOSE) up -d --build

down-dev:
	docker compose -f $(DEV_COMPOSE) down

up-prod:
	docker compose --env-file .env.prod -f $(PROD_COMPOSE) up -d --build

down-prod:
	docker compose --env-file .env.prod -f $(PROD_COMPOSE) down
