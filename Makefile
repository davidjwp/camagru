all:
	sudo hostsed add 

re:

clean:
	sudo docker compose -f ./docker-compose.yml down -v 

fclean:
	sudo docker system prune -af

.PHONY: all clean fclean