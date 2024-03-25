import axios from "axios";
import { jwtDecode } from "jwt-decode";

class Http {
  constructor() {
    this.instance = axios.create({
      baseURL: process.env.REACT_APP_API_BASE_URL,
      timeout: 10000,
    });
  }
}

const http = new Http().instance;

http.interceptors.request.use((config) => {
  /// setup
});

http.interceptors.response.use((response) => {
  return response;
});

export default http;
