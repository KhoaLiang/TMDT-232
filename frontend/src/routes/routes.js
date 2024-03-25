import { PATH } from "./paths";
import RequireAuth from "./RequireAuth";
import { HomePage, Unauthorized, Forbidden, NotFound } from "../pages/pages";
import Layout from "./Layout";
import LayoutAdmin from "./LayoutAdmin";

export const routes = [
  {
    element: <Layout />,
    path: PATH.ROOT,
    children: [
      {
        element: <HomePage />,
        path: PATH.HOME,
      },
    ],
  },
  {
    element: <LayoutAdmin />,
    path: PATH.ADMIN,
    children: [],
  },
  {
    path: PATH.UNAUTHORIZED,
    element: <Unauthorized />,
  },
  {
    path: PATH.FORBIDDEN,
    element: <Forbidden />,
  },
  {
    path: PATH.NOT_FOUND,
    element: <NotFound />,
  },
];
