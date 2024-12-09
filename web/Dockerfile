#FROM node:18.16.0-alpine3.16 AS build
FROM node:20-alpine3.20 AS build

WORKDIR /opt/www
COPY . /opt/www/

RUN npm install -g pnpm

ARG MINE_NODE_ENV=production

RUN echo "MINE_NODE_ENV=$MINE_NODE_ENV"

RUN pnpm install
RUN if [ "$MINE_NODE_ENV" = "development" ]; then pnpm build --mode development; fi && \
    if [ "$MINE_NODE_ENV" = "production" ]; then pnpm build --mode production; fi


FROM nginx:alpine AS production

COPY --from=build /opt/www/dist /usr/share/nginx/html
