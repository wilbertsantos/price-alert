import {
  mdiAccountCircle,
  mdiMonitor,
  mdiGithub,
  mdiLock,
  mdiAlertCircle,
  mdiSquareEditOutline,
  mdiTable,
  mdiViewList,
  mdiTelevisionGuide,
  mdiResponsive,
  mdiPalette,
  mdiReact,
  mdiAlarm,
} from "@mdi/js";

export default [
  {
    route: "dashboard",
    icon: mdiMonitor,
    label: "Dashboard",
  },
  {
    route: "alerts",
    label: "Alerts",
    icon: mdiAlarm,
  },
  {
    route: "tables",
    label: "Tables",
    icon: mdiTable,
  },
  {
    route: "form",
    label: "Forms",
    icon: mdiSquareEditOutline,
  },
  {
    route: "ui",
    label: "UI",
    icon: mdiTelevisionGuide,
  },
  {
    route: "responsive",
    label: "Responsive",
    icon: mdiResponsive,
  },
  {
    route: "style",
    label: "Styles",
    icon: mdiPalette,
  },
  {
    route: "userprofile",
    label: "Profile",
    icon: mdiAccountCircle,
  },
  {
    route: "login",
    label: "Login",
    icon: mdiLock,
  },
  {
    route: "error",
    label: "Error",
    icon: mdiAlertCircle,
  },
  {
    label: "Dropdown",
    icon: mdiViewList,
    menu: [
      {
        label: "Item One",
      },
      {
        label: "Item Two",
      },
    ],
  },
  {
    href: "https://github.com/justboil/admin-one-vue-tailwind",
    label: "GitHub",
    icon: mdiGithub,
    target: "_blank",
  },
  {
    href: "https://github.com/justboil/admin-one-react-tailwind",
    label: "React version",
    icon: mdiReact,
    target: "_blank",
  },
];
