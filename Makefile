all:
	docker compose up -d 
	docker exec -it controller chown www-data:www-data /var/www/html/tmp
	docker exec -it controller chown www-data:www-data /var/www/html/uploads

re:

clean:
	docker compose -f ./docker-compose.yml down -v 

fclean:
	docker system prune -af

.PHONY: all clean fclean