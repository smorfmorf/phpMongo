const testData = [
    {
        _id: 1,
        code: "30499",
        name: "Тында",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    {
        _id: 2,
        code: "30683",
        name: "Ерофей Павлович",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    {
        _id: 3,
        code: "30692",
        name: "Сковородино",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 4 */
    {
        _id: 4,
        code: "31168",
        name: "Аян",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 5 */
    {
        _id: 5,
        code: "31253",
        name: "Бомнак",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 6 */
    {
        _id: 6,
        code: "31295",
        name: "Магдагачи",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 7 */
    {
        _id: 7,
        code: "31300",
        name: "Зея",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 8 */
    {
        _id: 8,
        code: "31329",
        name: "Экимчан",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 9 */
    {
        _id: 9,
        code: "31348",
        name: "Бурукан",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 10 */
    {
        _id: 10,
        code: "31369",
        name: "Николаевск-на-Амуре",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 11 */
    {
        _id: 11,
        code: "31371",
        name: "Черняево",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 12 */
    {
        _id: 12,
        code: "31416",
        name: "Им.Полины Осипенко",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 13 */
    {
        _id: 13,
        code: "31418",
        name: "Веселая Горка",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 14 */
    {
        _id: 14,
        code: "31439",
        name: "Богородское",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 15 */
    {
        _id: 15,
        code: "31445",
        name: "Свободный",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 16 */
    {
        _id: 16,
        code: "31474",
        name: "Усть-Умальта",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 17 */
    {
        _id: 17,
        code: "31484",
        name: "Хуларин",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 18 */
    {
        _id: 18,
        code: "31512",
        name: "Благовещенск ОГМС",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 19 */
    {
        _id: 19,
        code: "31538",
        name: "Сутур",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 20 */
    {
        _id: 20,
        code: "31707",
        name: "Екатерино-Никольское",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 21 */
    {
        _id: 21,
        code: "31735",
        name: "Хабаровск",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 22 */
    {
        _id: 22,
        code: "31770",
        name: "Советская Гавань",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 23 */
    {
        _id: 23,
        code: "30385",
        name: "Усть-Нюкжа",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 24 */
    {
        _id: 24,
        code: "30686",
        name: "Игнашино",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 25 */
    {
        _id: 25,
        code: "31152",
        name: "Нелькан",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 26 */
    {
        _id: 26,
        code: "31174",
        name: "Большой Шантар",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 27 */
    {
        _id: 27,
        code: "31388",
        name: "Норск",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 28 */
    {
        _id: 28,
        code: "31478",
        name: "Софийский Прииск",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 29 */
    {
        _id: 29,
        code: "31442",
        name: "Шимановск",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 30 */
    {
        _id: 30,
        code: "31489",
        name: "Горин",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 31 */
    {
        _id: 31,
        code: "31521",
        name: "Братолюбовка",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 32 */
    {
        _id: 32,
        code: "31527",
        name: "Завитая",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 33 */
    {
        _id: 33,
        code: "31532",
        name: "Чекунда",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 34 */
    {
        _id: 34,
        code: "31534",
        name: "Сектагли",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 35 */
    {
        _id: 35,
        code: "31587",
        name: "Поярково",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 36 */
    {
        _id: 36,
        code: "31594",
        name: "Архара",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 37 */
    {
        _id: 37,
        code: "31624",
        name: "Урми",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 38 */
    {
        _id: 38,
        code: "31632",
        name: "Кур",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 39 */
    {
        _id: 39,
        code: "31655",
        name: "Троицкое",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 40 */
    {
        _id: 40,
        code: "31702",
        name: "Облучье",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 41 */
    {
        _id: 41,
        code: "31713",
        name: "Биробиджан",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 42 */
    {
        _id: 42,
        code: "31725",
        name: "Смидович",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 43 */
    {
        _id: 43,
        code: "31754",
        name: "Тивяку",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 44 */
    {
        _id: 44,
        code: "31801",
        name: "Гвасюги",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 45 */
    {
        _id: 45,
        code: "30695",
        name: "Джалинда",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 46 */
    {
        _id: 46,
        code: "31263",
        name: "Локшак",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 47 */
    {
        _id: 47,
        code: "31299",
        name: "Тыгда",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 48 */
    {
        _id: 48,
        code: "31373",
        name: "Октябрьский Прииск",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 49 */
    {
        _id: 49,
        code: "31392",
        name: "Стойба",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    /* 50 */
    {
        _id: 50,
        code: "31443",
        name: "Мазаново",
        hours: [0, 6, 12, 18],
        noVisualHours: [],
        loc: "hbrw",
        header: "СМ",
    },
    // и так далее для всех объектов...
];

export default testData;
