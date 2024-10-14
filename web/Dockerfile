#FROM node:18.16.0-alpine3.16 AS build
FROM node:lts-alpine AS build

WORKDIR /opt/www
COPY . /opt/www/

ARG MINE_NODE_ENV=production

ENV MINE_NODE_ENV $MINE_NODE_ENV

RUN echo "MINE_NODE_ENV=$MINE_NODE_ENV"


RUN pnpm install --frozen-lockfile --prefer-frozen-lockfile && \
    if [ "$MINE_NODE_ENV" = "development" ]; then pnpm build --mode development; fi && \
    if [ "$MINE_NODE_ENV" = "production" ]; then pnpm build --mode production; fi && \
    pnpm cache clear --force

RUN pnpm build --mode production

FROM nginx:alpine

COPY --from=build /opt/www/dist /usr/share/nginx/html
