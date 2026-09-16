up:
	docker compose up -d --build
	@echo "Ожидание готовности php-контейнера..."
	@until docker compose exec -T php true 2>/dev/null; do sleep 1; done
	@$(MAKE) set-perms
	@$(MAKE) setup
	@echo ""
	@echo "Готово. Откройте: http://localhost:8080/bitrixsetup.php"

setup:
	@echo "Скачивание bitrixsetup.php..."
	@docker compose exec -T php bash -c "\
		curl -fsSL https://www.1c-bitrix.ru/download/scripts/bitrixsetup.php \
			-o /var/www/html/bitrixsetup.php && \
		chmod 644 /var/www/html/bitrixsetup.php"
	@echo "bitrixsetup.php загружен"
	@docker compose exec php composer install

set-perms:
	@sudo chown -R $(shell id -u):$(shell id -g) www
	@echo "Права на www изменены"

down:
	docker compose down