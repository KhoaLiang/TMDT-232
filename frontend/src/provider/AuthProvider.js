import React, { createContext, useContext } from "react";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  // information user
  return <AuthContext.Provider value={{}}>{children}</AuthContext.Provider>;
};

export const useAuth = () => {
  return useContext(AuthContext);
};
