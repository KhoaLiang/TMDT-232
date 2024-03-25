import React from "react";
import "./App.scss";
import { routes } from "./routes/routes";
import { useRoutes } from "react-router-dom";

function App() {
  const element = useRoutes(routes);
  return <div className="h-full">{element}</div>;
}

export default App;
