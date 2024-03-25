FROM node:20-alpine as build

WORKDIR /app

COPY frontend/package*.json ./

RUN npm install

COPY frontend .

RUN npm run build

FROM nginx:stable-alpine

COPY --from=build /app/build /usr/share/nginx/html

COPY frontend/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]