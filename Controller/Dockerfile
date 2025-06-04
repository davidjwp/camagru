FROM	node:latest

EXPOSE	${SERV_PORT}

ADD		${SERVF}

ARG		SERV_PORT=${SERV_PORT} 
ARG		SERV_ADDRESS=${SERV_ADDRESS}
ARG		SERVF=${SERVF}

RUN		apt-get update && apt-get install vim -y  

CMD 	["node", ${SERV_FILE_NAME}]