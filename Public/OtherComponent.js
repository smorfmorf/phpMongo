export default {
  template: /* html */ `
  <div class="btn btn-primary" @click="value++">Another <em>component {{value}}</em></div>
  `,

  data() {
    return {
      message: "This is OtherComponent",
      value: 0,
    };
  },
  methods: {
    log() {
      console.log(this.message);
    },
  },
};
