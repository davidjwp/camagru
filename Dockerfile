FROM alpine

RUN echo hello >> T

CMD ["sleep", "1000"]