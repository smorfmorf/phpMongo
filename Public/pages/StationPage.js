export default {
  template: /* html */ `
     <!-- Компонент СТАНЦИй -->
            <div class="stations data ">
                <div class="data-body relative">
                    <div class="data-body__left">
                        <div class="data-group">
                            <p>Группировка: </p>
                            <ul class="data-group__list">
                                <li class="">
                                    <a @click="groupBy('station')" class="group-stations">По станции</a>
                                </li>
                                <li>
                                    <a @click="groupBy('type')" class="group-type">По типу</a>
                                </li>
                                <li>
                                    <select v-model="selectedRegion" @change="groupBy('region')" class="group-region">
                                        <option :value="null">Выбрать регион</option>
                                        <option v-for="region in uniqueRegions" :value="region">Region: {{ region }}
                                        </option>
                                    </select>
                                </li>
                                <li>
                                    <a @click="resetGrouping" class="group-reset">Сбросить</a>
                                </li>
                            </ul>

                            <div>
                                <input type="text" v-model="searchValue" class="form-control"
                                    placeholder="Поиск по названию или коду">
                            </div>
                        </div>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-exclamation-circle"></i></th>
                                    <th>Индекс</th>
                                    <th>Название</th>
                                    <th>Тип</th>
                                    <th>Регион</th>
                                    <th><i cla ss='bi bi-chat-dots'></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="6">
                                        <div class="loader-table">
                                            <div class="lds-default">
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Рендер основного контента:  -->
                                <template v-else>
                                    <template v-for="(group, groupName) in groupedStations" :key="groupName">
                                        <tr class="group-header" v-if="grouping">
                                            <td colspan="6"><strong>{{ groupName }}</strong></td>
                                        </tr>
                                        <tr v-for="station in group" :key="station.siteId">
                                            <td>
                                                <div class="station-warning">
                                                    <i :class="['bi', 'bi-circle-fill', station.lastStatus === 'OK' ? 'text-success' : 'warning-ping']"
                                                        :title="'Ping: ' + (station.lastStatus || 'Не проверен') + ' Дата пинга:' + (new Date(station.dateUpdate).toLocaleString() || 'Не проверен')"
                                                        @click="showPingTelnetPopover(station, $event)"
                                                        style="cursor: pointer;"></i>
                                                    <i :class="['bi', 'bi-circle-fill', station.telnetStatus === 'OK' ? 'text-success' : 'station-telnet']"
                                                        :title="'Telnet: ' + (station.telnetStatus || 'Не проверен')"
                                                        @click="showPingTelnetPopover(station, $event)"
                                                        style="cursor: pointer;"></i>
                                                </div>
                                            </td>
                                            <td>{{ station.stationCode }}</td>
                                            <td class="hover:bg-slate-300! hover:scale-105 cursor-pointer hover:font-bold  transition" @click="setShowModal(station)" >{{ station.stationName }}</td>
                                            <td>{{ station.stationType }}</td>
                                            <td>{{ station.stationRegion }}</td>
                                            <td>
                                                <i class='bi bi-chat-dots' style="cursor: pointer;"
                                                    @click="showMessages(station)"></i>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>


                        <!-- Модальное окно для сообщений -->
                        <div class="modal fade" id="modalMessage" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Содержимое модального окна -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary close-modal"
                                            data-bs-dismiss="modal">Закрыть</button>
                                        <button type="button"
                                            class="btn btn-success save-station__item">Сохранить</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Панель сообщений -->

                    <template v-if="showMessagesPanel">
                        <div class="data-body__message" v-if="messages.length > 0">
                            <div class="messages max-h-[700px] overflow-y-scroll" style="display: block;">
                                <div class="messages-header">
                                    Сообщения станции {{ selectedStation.stationName }}

                                    <i class="messages_close bi bi-x" style="cursor: pointer;"
                                        @click="closeMessages"></i>
                                </div>
                                <div class="messages-body ">
                                    <div class="data-group">
                                        <p>Группировка: </p>
                                        <ul class="data-group__list">
                                            <li>
                                                <a @click="filterMessages('day')"
                                                    class="message__group-by__day">День</a>
                                            </li>
                                            <li class="">
                                                <a @click="filterMessages('week')"
                                                    class="message__group-by__week">Неделя</a>
                                            </li>
                                        </ul>

                                        <div style="margin-top: 10px;">
                                            <input type="text" v-model="searchQuery" placeholder="Поиск по заголовку..."
                                                class="form-control">
                                        </div>


                                        <!-- Селектор количества элементов на странице -->
                                        <div style="margin-top: 10px;">
                                            <label>Показать:</label>
                                            <select v-model="limit" class="form-select">
                                                <option disabled value="">Выберите количество</option>
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="20">20</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                            <div>Всего Записей: {{ messages.length }}</div>
                                        </div>

                                    </div>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Заголовок</th>
                                                <th>nil</th>
                                                <th>Получено</th>
                                                <th>Текст</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(message , index) in filteredMessages" :key="index"
                                                @click="showMessageDetail(message)" style="cursor: pointer;">
                                                <td>{{ message.msg_key }}</td>
                                                <td>{{ message.nil }}</td>
                                                <td>{{ formatDate(message.time) }}</td>
                                                <td class="max-w-xs">
                                                    <span class="inline-block overflow-hidden truncate w-full">
                                                        {{ message.text }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div style="margin-top: 15px;">
                                        Показано: {{ filteredMessages.length }} сообщений
                                    </div>

                                    <div class="pagination flex gap-[10px] items-center">
                                        Страница:
                                        <button v-for="page in totalPages" :key="page"  @click="currentPage = page"
                                            class="w-[30px] h-[30px] rounded-full grid place-items-center transition"                                
                                            :class="currentPage === page ? 'bg-blue-600 text-white': 'bg-gray-300 text-black hover:bg-gray-400'"
                                            >
                                            {{ page }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="animate-pulse text-xl">
                            Загрузка сообщений...
                        </div>
                    </template>


                    <!-- Popover для ping/telnet -->
                    <div v-if="showPopover" id="pingTelnetPopover" :style="{
                             top: popoverPosition.top + 'px',
                             left: popoverPosition.left + 'px',
                             opacity: '90%'
                         }">
                        <div class="popover-header">
                            <h3 class="popover-title">Проверить связь</h3>
                            <button type="button" class="close-popover" @click="closePopover">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <div class="popover-body">
                            <button class="btn btn-secondary btn-sm" @click="pingStation">
                                Ping
                            </button>
                            <button class="btn btn-secondary btn-sm" @click="telnetStation">
                                Telnet
                            </button>
                        </div>
                    </div>
                </div>

    <transition name="fade">
      <teleport to="#modalRoot">
        <div
          v-if="showModal"
          class="modal-overlay fixed inset-0 z-20 bg-black/50 grid place-items-center"
        >
          <div class="modal-window bg-white w-[1100px] max-h-[90vh] overflow-y-auto rounded-xl shadow-xl p-6">
            
            <!-- header -->
            <div class="station-item__header flex items-center justify-between border-b pb-3 mb-5">
              <div class="back__link text-blue-600 cursor-pointer transition hover:-translate-x-1 text-xl" @click="showModal = false">
                ← Назад
              </div>
              <div class="font-bold text-lg">
                Станция <span class="index-station">{{ selectedStation?.stationCode }}</span>
              </div>
            </div>

            <!-- form -->
            <form id="form-item__station" class="space-y-6">

              <!-- row: индекс и название -->
              <div class="grid grid-cols-2 gap-6">
                <div class="text-xl">
                  <label for="indexStation" class="block font-medium text-gray-700 ">Индекс</label>
                  <input disabled type="text" id="indexStation" v-model="selectedStation.stationCode"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-md p-2 text-lg"/>
                </div>
                <div class="text-xl">
                  <label for="nameStation" class="block font-medium text-gray-700 ">Название</label>
                  <input disabled type="text" id="nameStation" v-model="selectedStation.stationName"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-md p-2 text-lg "/>
                </div>
              </div>

              <!-- tabs -->
              <div>
                <div class="border-b border-gray-200">
                  <nav class="flex items-center gap-[16px]" aria-label="Tabs">
                    <button type="button"
                      class="tab-btn pb-2 border-b-2 font-medium text-sm"
                      :class="activeTab === 'instruments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                      @click="activeTab = 'instruments'">
                      Инструменты по станции
                    </button>
                    <button type="button"
                      class="tab-btn pb-2 border-b-2 font-medium text-sm"
                      :class="activeTab === 'attributes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                      @click="activeTab = 'attributes'">
                      Атрибуты по станции
                    </button>
                  </nav>
                </div>

                <div class="mt-4">
                  <!-- Tab content -->
                  <div v-show="activeTab === 'instruments'" class="space-y-4">
                    <div id="instrumentBlock" class="bg-gray-50 border rounded p-3 grid gap-[10px]">
                      <!-- здесь будет динамика по инструментам -->
                      <p class="text-gray-500 text-sm">Блок инструментов станции</p>

                      <div class="grid gap-2 ">
                        <label class="text-gray-500 text-sm grid! gap-1">
                        <p class="text-xl text-black">
                        Порт и адрес подключения Telnet
                        </p>
                        <input type="text" class="w-full p-2 bg-slate-200 text-black text-lg outline-none border-none rounded-lg" v-model="forTelnet.description" />
                        </label>
                      <p class="w-fit p-2 border border-gray-300 text-black text-lg rounded-xl shadow-lg shadow-black cursor-pointer hover:bg-gray-300 transition hover:scale-105"  @click="changeAddressModal(forTelnet)">Редактировать</p>
                      </div>

                      <div class="grid gap-2 ">
                        <label class="text-gray-500 text-sm grid! gap-1">
                      <p class="text-xl  text-black">
                        IP адрес поста
                      </p>
                        <input type="text" class="w-full p-2 bg-slate-200 text-black text-lg outline-none border-none rounded-lg" v-model="forIP.description" />
                        </label>
                        <p class="w-fit p-2 border border-gray-300 text-black text-lg rounded-xl shadow-lg shadow-black cursor-pointer hover:bg-gray-300 transition hover:scale-105" @click="changeAddressModal(forIP)">Редактировать</p>
                      </div>
                    </div>
                  </div>
                  <!--Конец инструментов -->

                  <!-- Атрибуты -->
                  <div v-show="activeTab === 'attributes'" class="space-y-4">
                    <div class="flex items-center gap-3">
                      <select id="attrId" class="rounded border-gray-300 shadow-lg focus:ring-blue-500 p-3" v-model="selectedAttribute">
                        <option disabled value="">Выбрать атрибут</option>
                        <option v-for="attr in filteredAttributesForSelector"
                          :key="attr.attrTypeId"
                          :value="attr">
                          {{attr.desc}}
                        </option>
                      </select>

                      <input v-model="inputOption" type="text" id="attrNew" placeholder="Значение"
                        class="flex-1 rounded border-gray-300 shadow-sm focus:ring-blue-500 p-3"/>
                      <button type="button" class="btn btn-success bg-green-600 text-white px-4 py-2 rounded"
                        @click="addAttribute()">
                        Добавить атрибут
                      </button>
                    </div>
                    <table class="min-w-full border divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Наименование</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Значение</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Обновление</th>
                        </tr>
                      </thead>
                      <tbody id="attrBlock" class="divide-y divide-gray-200">
                        <tr v-for="(attr, idx) in filteredAttributes" :key="idx">
                          <td class="px-3 py-2">{{ attr.desc }}</td>
                          <td class="px-3 py-2"><input type="text" v-model="attr.value"/></td>
                          <td class="px-3 py-2">{{ formatDate(attr.date) }}</td>
                          <td class="px-3 py-2">
                          <button class="p-2 bg-green-400 text-white rounded hover:bg-green-500 active:bg-green-600" @click="updateStationAttribute(attr)">Обновить</button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                </div>
                </div>

              <!-- save btn -->
              <div class="flex justify-end">
                <button type="button" class="mt-2 bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 active:bg-blue-800">
                  Сохранить
                </button>
              </div>
            </form>

          </div>
        </div>
      </teleport>
    </transition>

  </div>
  `,

  data() {
    return {
      baseURL: window.location.origin,
      // ? Данные для модалки
      showModal: false,
      selectedStation: null, // Выбранная станция (для сообщений и телепорта)
      activeTab: "instruments",
      attributes: [],
      selectedAttribute: "",
      inputOption: "",

      forIp: "",
      forTelnet: "",

      // ? Для пагинации (забил пока на нее)
      limit: 10,
      currentPage: 1, // 👈 добавляем

      //? Основные данные
      stations: [], // список станций
      loading: true, // загрузка
      selectedRegion: null, // выборка по региону

      //? Для поиска
      searchValue: "", // Для поиска
      uniqueRegions: [], // для фильтрации по регионам
      grouping: null, // для фильтрации (групировка по Name, Type, Region из данных stations)

      //? Для блока сообщений
      searchQuery: "",
      showMessagesPanel: false, // показываем блок сообщений
      messages: [], // Для блока сообщений

      //? Для ping/telnet popover
      currentPopoverStation: null, // какую станцию выбрали для пинга или телнета
      showPopover: false, // показываем модалку
      popoverPosition: {
        // чтобы модалка не куда не убежала
        top: 0,
        left: 0,
      },
    };
  },
  //! Методы жизненого цикла:
  async mounted() {
    await this.loadStations();
    this.setupPopoverListeners();

    // Проверяем все станции при загрузке
    const now = new Date();
    for (const station of this.stations) {
      // console.log("✌️station ;;;;;;;;;;;;;;;;;;;--->", station);
      if (!station.dateUpdate) {
        // Никогда не пинговали → пингуем
        await this.pingStation(station, true);
        continue;
      }

      const lastUpdate = new Date(station.dateUpdate);
      const diffMs = now - lastUpdate;
      const diffHours = diffMs / (1000 * 60 * 60);

      if (diffHours >= 1) {
        // прошло больше часа → пингуем снова
        await this.pingStation(station, true);
      }
    }
  },
  //! ....................................
  computed: {
    totalPages() {
      // Когда меняем лимит или длина сообщений пересчитываем кол-во страниц
      return this.getPageCount(this.messages.length, this.limit);
    },

    filteredAttributesForSelector() {
      return this.attributes.filter((attr) => !attr.value);
    },
    filteredAttributes() {
      return this.attributes.filter((attr) => attr.value);
    },
    filteredMessages() {
      // Простая фильтрация
      const search = this.searchQuery.toLowerCase();
      const filtered = this.messages.filter((msg) => {
        return (
          msg.msg_key.toLowerCase().includes(search) ||
          msg.text.toLowerCase().includes(search)
        );
      });

      const start = (this.currentPage - 1) * this.limit;
      console.log("✌️start --->", start);
      const end = Number(start) + Number(this.limit);
      console.log("✌️end --->", end);

      return filtered.slice(start, end);
    },
    // Фильтрация станций по поисковому запросу
    filteredStations() {
      if (!this.searchValue) return this.stations;

      const searchLower = this.searchValue.toLowerCase();
      return this.stations.filter(
        (station) =>
          station.stationName.toLowerCase().includes(searchLower) ||
          station.stationCode.toLowerCase().includes(searchLower)
      );
    },

    // Группировка станций
    groupedStations() {
      let stationsToGroup = this.filteredStations;

      // Если выбран регион, фильтруем
      if (this.selectedRegion) {
        stationsToGroup = stationsToGroup.filter(
          (station) => station.stationRegion == this.selectedRegion
        );
      }

      // Если не выбрана группировка, просто возвращаем все станции
      if (!this.grouping) {
        return {
          "Все станции": stationsToGroup,
        };
      }

      // Группировка по нужному полю
      const grouped = {};

      stationsToGroup.forEach((station) => {
        let key;

        switch (this.grouping) {
          case "station":
            key = station.stationName;
            break;
          case "type":
            key = station.stationType;
            break;
          case "region":
            key = station.stationRegion;
            break;
          default:
            key = "Другие";
        }

        if (!grouped[key]) {
          grouped[key] = [];
        }

        grouped[key].push(station);
      });
      console.log("✌️grouped --->", grouped);

      return grouped;
    },
  },
  watch: {
    limit(newVal, oldVal) {
      this.currentPage = 1; // 👈 сбрасываем при смене лимита
    },
  },
  methods: {
    async updateStationAttribute(attr) {
      console.log("✌️attr --->", attr);
      console.log(this.selectedStation);

      const newObj = {
        siteId: this.selectedStation.siteId,
        attrValues: [attr.value],
        attrIds: [attr.attrTypeId],
      };

      const { data } = await axios.get(
        `${this.baseURL}/server/setStationAttributeData.php`,
        {
          params: {
            siteId: newObj.siteId,
            attrValues: newObj.attrValues,
            attrIds: newObj.attrIds,
          },
        }
      );
      console.log("✌️data --->", data);
      console.log("✌️newObj --->", newObj);

      this.attributes = this.attributes.map((a) => {
        return a.attrTypeId === attr.attrTypeId ? attr : a;
      });
    },

    addAttribute() {
      console.log(this.selectedAttribute);

      console.log({ ...this.selectedAttribute, value: this.inputOption });

      const newObj = {
        ...this.selectedAttribute,
        value: this.inputOption,
      };
      this.attributes = this.attributes.map((attr) => {
        return attr.attrTypeId === newObj.attrTypeId ? newObj : attr;
      });

      this.inputOption = "";

      console.log("Все заеб");
      console.log(this.attributes);
    },

    async setShowModal(station) {
      this.selectedStation = station;
      console.log("✌️selectedStation --->", this.selectedStation);
      this.showModal = !this.showModal;
      this.activeTab = "instruments";

      const { data } = await axios.get(
        `${this.baseURL}/server/getStationInstrumentListBySiteId.php`,
        {
          params: {
            siteId: this.selectedStation.siteId,
          },
        }
      );

      this.forIP = data[1];
      this.forTelnet = data[0];

      console.log("✌️data --->", data);

      const response = await axios.get(
        `${this.baseURL}/server/getStationAttributeListBySiteId.php`,
        {
          params: {
            siteId: this.selectedStation.siteId,
          },
        }
      );
      this.attributes = response.data;
      console.log("✌️attributes --->", this.attributes);
    },

    async changeAddressModal(ObjProxy) {
      console.log("✌️value --->", ObjProxy);
      const { data } = await axios.get(
        `${this.baseURL}/server/setStationInstrumentData.php`,
        {
          params: {
            siteId: this.selectedStation.siteId,
            values: [ObjProxy.description],
            instrumentIds: [ObjProxy.instrumentId],
          },
        }
      );
      console.log("✌️data --->", data);
    },

    async loadStations() {
      this.loading = true;
      try {
        const { data } = await axios.get(
          `${this.baseURL}/server/stations.php`,
          {
            params: {
              region: 27,
              type: "1,5",
            },
          }
        );
        console.log("✌️data $$$--->", data);
        this.stations = data;
        this.uniqueRegions = [
          ...new Set(this.stations.map((s) => s.stationRegion)),
        ];
      } catch (error) {
        console.error("Ошибка загрузки станций:", error);
      } finally {
        this.loading = false;
      }
    },

    // Ping станции (всегда localhost)
    async pingStation(currentStation = {}, silent = false) {
      if (!this.currentPopoverStation && !currentStation) return;
      const station = this.currentPopoverStation || currentStation;
      this.showPopover = false;
      console.log("✌️station PING--->", station);

      let status = "ERROR"; // по умолчанию ошибка
      let stationIP = null;

      //! ШАГ 1 Получаем Ip станции, затем Ping
      try {
        //? Получаем IP станции по ID
        const response = await axios.get(
          `${this.baseURL}/server/getStationInstrumentDataById.php`,
          {
            params: {
              siteId: station.siteId,
              instrumentId: 4,
            },
          }
        );
        stationIP = response.data;

        console.log("✌️response--->", response);
        console.log(`Ping localhost для станции: ${station.stationName}`);

        //? Пингуем станцию
        const { data } = await axios.get(`${this.baseURL}/server/ping.php`, {
          params: {
            host: stationIP,
          },
        });

        console.log("✌️data --->", data);

        status = data.status === "success" ? "OK" : "ERROR";

        if (!silent) {
          const packetsSent = data.packets_sent || 4;
          const packetsReceived = data.packets_received || 0;
          const packetsLost = packetsSent - packetsReceived;
          const packetLoss = data.packet_loss || "0%";
          const statustext = status === "OK" ? "УСПЕХ ✅" : "ОШИБКА ❌";

          alert(`📊 Ping статистика для ${station.stationName}:
                            📍 Хост: ${stationIP}
                            ✅ Отправлено пакетов: ${packetsSent}
                            ✅ Получено пакетов: ${packetsReceived}
                            ❌ Потеряно пакетов: ${packetsLost}
                            📉 Потери: ${packetLoss}
                            📌 Статус: ${statustext}`);
        }
      } catch (error) {
        console.error(`Ping для "${station.stationName}": Ошибка`, error);
        status = "ERROR";

        if (!silent) {
          alert(`❌ Ошибка ping для ${station.stationName}:\n${error.message}`);
        }
      }

      //! ШАГ 2 Обновляем локально данные во Vue
      // 👉 обновляем объект станции всегда
      const newObj = {
        ...station,
        lastStatus: status,
        dateUpdate: new Date().toISOString(),
      };

      this.stations = this.stations.map((s) =>
        s.siteId === station.siteId ? newObj : s
      );
      console.log("✌️stations2 --->", this.stations);

      //! Шаг 3 обновляем в БД данные
      try {
        const res = await axios.post(
          `${this.baseURL}/server/updateStation.php`,
          {
            siteId: station.siteId,
            lastStatus: newObj.lastStatus,
            dateUpdate: newObj.dateUpdate,
          }
        );
        console.log("Обновление в БД:", res.data);
      } catch (err) {
        console.error("Ошибка при обновлении в БД:", err);
      }
    },

    async showMessages(station) {
      this.selectedStation = station;
      this.showMessagesPanel = true;
      this.messages = [];
      console.log("✌️selectedStation --->", this.selectedStation);
      const { data } = await axios.get(
        `${this.baseURL}/server/getStationsTelegrams.php`,
        {
          params: {
            stationCode: this.selectedStation.stationCode,
            siteId: this.selectedStation.siteId,
          },
        }
      );
      this.messages = data.rep;
    },

    closeMessages() {
      this.showMessagesPanel = false;
      this.selectedStation = null;
      this.messages = [];
    },

    formatDate(timeString) {
      if (!timeString) return "";

      // ISO формат (2025-07-29T00:04:25Z)
      const date = new Date(timeString);

      if (isNaN(date)) return timeString; // если не смог распарсить

      const day = date.getDate().toString().padStart(2, "0");
      const month = (date.getMonth() + 1).toString().padStart(2, "0");
      const year = date.getFullYear();
      const hours = date.getHours().toString().padStart(2, "0");
      const minutes = date.getMinutes().toString().padStart(2, "0");
      const seconds = date.getSeconds().toString().padStart(2, "0");

      return `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`;
    },

    groupBy(type) {
      this.grouping = type;
    },

    resetGrouping() {
      this.grouping = null;
      this.selectedRegion = null;
    },

    // ------------------------------------------------------------------------------------------------

    // ------------------------------------------------------------------------------------------------

    //! Telnet станции (тестовая функция)
    async telnetStation() {
      if (!this.currentPopoverStation) return;
      const station = this.currentPopoverStation;

      this.showPopover = false;

      const response = await axios.get(
        `${this.baseURL}/server/getStationInstrumentDataById.php`,
        {
          params: {
            siteId: station.siteId,
            instrumentId: 1,
          },
        }
      );

      const telnetIP = response.data.split(":");
      console.log("✌️telnetIP --->", telnetIP);

      try {
        console.log(`Telnet localhost для станции: ${station.stationName}`);

        const { data } = await axios.get(`${this.baseURL}/server/telnet.php`, {
          params: {
            host: telnetIP[0],
            port: telnetIP[1],
          },
        });

        // Обновляем статус telnet
        station.telnetStatus = data.status === "success" ? "OK" : "ERROR";

        console.log("✌️data --->", data);
        alert(data.message);
      } catch (error) {
        console.error(`Telnet для "${station.stationName}": Ошибка`, error);
        station.telnetStatus = "ERROR";
        alert(`❌ Ошибка Telnet для ${station.stationName}:\n${error.message}`);
      }
    },

    // ------------------------------------------------------------------------------------------------
    // Показать popover для ping/telnet и установить станцию
    showPingTelnetPopover(station, event) {
      this.currentPopoverStation = station;
      this.showPopover = true;

      // Родительский контейнер (тот, что relative)
      const container = document.querySelector(".data-body");
      const containerRect = container.getBoundingClientRect();

      this.popoverPosition = {
        top: event.clientY - containerRect.top + 10,
        left: event.clientX - containerRect.left + 10,
      };
    },

    // Закрыть popover и сбросить данные
    closePopover() {
      this.showPopover = false;
      this.currentPopoverStation = null;
    },

    // При клике вне popover -> закрыть popover
    setupPopoverListeners() {
      document.addEventListener("click", (e) => {
        if (
          this.showPopover &&
          !e.target.closest("#pingTelnetPopover") &&
          !e.target.closest(".station-warning")
        ) {
          this.closePopover();
        }
      });
      // Закрытие по клавише Escape
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && this.showPopover) {
          this.closePopover();
        }
      });
    },
    // ----------------------------------------- Когда кликаем по сообщению (в самом блоке сообщений)... переделать модалку бд пока хз
    showMessageDetail(message) {
      const modal = new bootstrap.Modal(
        document.getElementById("modalMessage")
      );
      console.log("✌️modal --->", modal);
      document
        .getElementById("modalMessage")
        .querySelector(".modal-body").innerHTML = `
                    <div>
                        <h6>Детали сообщения</h6>
                        <p><strong>Тип:</strong> ${message.msg_key}</p>
                        <p><strong>Время:</strong> ${this.formatDate(
                          message.time
                        )}</p>
                        <p><strong>Текст:</strong> ${message.text}</p>
                    </div>
                `;
      modal.show();
    },

    //! utils -----------------------------------
    // подсчет кол-во страниц
    getPageCount(totalCount, limit = 5) {
      const pages = Math.ceil(totalCount / limit);

      let pagesArray = [];

      for (let i = 1; i <= pages; i++) {
        pagesArray.push(i);
      }

      return pagesArray;
    },
  },
};
