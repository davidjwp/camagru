all:
	mkdir -p ./controller/html/tmp
	mkdir -p ./controller/html/uploads
	chmod 755 ./controller/html/tmp
	chmod 755 ./controller/html/uploads
	docker compose up -d 
	until docker exec -it controller true 2>/dev/null; do sleep 1; done
	docker exec -it controller chown www-data:www-data /var/www/html/tmp
	docker exec -it controller chown www-data:www-data /var/www/html/uploads

re:
	mkdir -p ./controller/html/tmp
	mkdir -p ./controller/html/uploads
	chmod 755 ./controller/html/tmp
	chmod 755 ./controller/html/uploads
	docker compose down
	docker compose up -d --build
	until docker exec -it controller true 2>/dev/null; do sleep 1; done
	docker exec -it controller chown www-data:www-data /var/www/html/tmp
	docker exec -it controller chown www-data:www-data /var/www/html/uploads

clean:
	docker compose down -v

fclean:
	docker compose up -d 
	docker exec -it controller rm -fr /var/www/html/tmp
	docker exec -it controller rm -fr /var/www/html/uploads
	docker compose down
	docker system prune -af
	docker volume rm mysql

.PHONY: all clean fclean