var p=Object.defineProperty;var i=Object.getOwnPropertySymbols;var h=Object.prototype.hasOwnProperty,l=Object.prototype.propertyIsEnumerable;var r=(o,s,e)=>s in o?p(o,s,{enumerable:!0,configurable:!0,writable:!0,value:e}):o[s]=e,t=(o,s)=>{for(var e in s||(s={}))h.call(s,e)&&r(o,e,s[e]);if(i)for(var e of i(s))l.call(s,e)&&r(o,e,s[e]);return o};import{a as $,m as n,g as d}from"./index.d328c175.js";const y={computed:t({},$(["currentPost","options","dynamicOptions","settings"])),methods:{updateAioseo(){this.$set(this.$store.state,"currentPost",n(t({},this.$store.state.currentPost),t({},window.aioseo.currentPost)))}},mounted(){this.$nextTick(()=>{window.addEventListener("updateAioseo",this.updateAioseo)})},beforeDestroy(){window.removeEventListener("updateAioseo",this.updateAioseo)},async created(){const{options:o,settings:s,dynamicOptions:e,currentPost:a,internalOptions:u,tags:c}=await d();this.$set(this.$store.state,"options",n(t({},this.$store.state.options),t({},o))),this.$set(this.$store.state,"settings",n(t({},this.$store.state.settings),t({},s))),this.$set(this.$store.state,"dynamicOptions",n(t({},this.$store.state.dynamicOptions),t({},e))),this.$set(this.$store.state,"currentPost",n(t({},this.$store.state.currentPost),t({},a))),this.$set(this.$store.state,"internalOptions",n(t({},this.$store.state.internalOptions),t({},u))),this.$set(this.$store.state,"tags",n(t({},this.$store.state.tags),t({},c)))}};export{y as S};
