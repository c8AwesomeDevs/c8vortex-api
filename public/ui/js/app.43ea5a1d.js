(function(){"use strict";var t={5971:function(t,e,r){var n=r(144),i=r(3726),o=function(){var t=this,e=t._self._c;return e(i.Z,{attrs:{id:"inspire"}},[e("router-view")],1)},a=[],l={data:()=>({drawer:null})},s=l,d=r(1001),c=(0,d.Z)(s,o,a,!1,null,null,null),u=c.exports,h=r(8345),p=r(8506),f=r(9396),g=r(3381),y=r(7024),m=r(1819),v=r(7690),x=r(7970),b=r(1667),w=r(607),_=r(3102),Z=r(1157),k=r(7894),T=r(5439),S=r(2515),D=r(3118),C=function(){var t=this,e=t._self._c;return e("div",[e(p.Z,{attrs:{app:"",color:"white",flat:""}},[e(m.Z,{staticClass:"py-0 fill-height"},[e(f.Z,{staticClass:"mr-10",attrs:{color:"grey darken-1",size:"32"}}),t._l(t.links,(function(r){return e(g.Z,{key:r,attrs:{text:""}},[t._v(" "+t._s(r)+" ")])})),e(S.Z),e(Z.Z,{attrs:{"max-width":"260"}},[e(D.Z,{attrs:{dense:"",flat:"","hide-details":"",rounded:"","solo-inverted":""}})],1)],2)],1),e(_.Z,{staticClass:"grey lighten-3"},[e(m.Z,[e(k.Z,[e(y.Z,{attrs:{cols:"2"}},[e(T.Z,{attrs:{rounded:"lg"}},[e(x.Z,{attrs:{color:"transparent"}},[t._l(5,(function(r){return e(b.Z,{key:r,attrs:{link:""}},[e(w.km,[e(w.V9,[t._v(" List Item "+t._s(r)+" ")])],1)],1)})),e(v.Z,{staticClass:"my-2"}),e(b.Z,{attrs:{link:"",color:"grey lighten-4"}},[e(w.km,[e(w.V9,[t._v(" Refresh ")])],1)],1)],2)],1)],1),e(y.Z,[e(T.Z,{attrs:{"min-height":"70vh",rounded:"lg"}},[e("router-view")],1)],1)],1)],1)],1)],1)},P=[],O={data:()=>({links:["Dashboard","Messages","Profile","Updates"]})},j=O,M=(0,d.Z)(j,C,P,!1,null,null,null),H=M.exports,F=function(){var t=this,e=t._self._c;return e("div",[e(k.Z,[e(y.Z,{attrs:{cols:"4"}},[e("duvals-triangle-one")],1),e(y.Z,{attrs:{cols:"4"}})],1)],1)},N=[],A=function(){var t=this,e=t._self._c;return e("div",{attrs:{id:"triangle1"}})},E=[],Y=r(1160);class I{constructor(t,e){this.container=t,this.config=e,this.chart=null,this.xScale=null,this.yScale=null}test(){return"Test"}load(t){var e=this.config,r={PD:void 0!=e.pd_color?e.pd_color:"pink",T1:void 0!=e.t1_color?e.t1_color:"orange",T2:void 0!=e.t2_color?e.t2_color:"LimeGreen",T3:void 0!=e.t3_color?e.t3_color:"HotPink",DT:void 0!=e.dt_color?e.dt_color:"blue",D1:void 0!=e.d1_color?e.d1_color:"SkyBlue",D2:void 0!=e.d2_color?e.d2_color:"#679A00"},n=[{x:0,y:57.73502692},{x:-1,y:56.003},{x:1,y:56.003},{x:-2,y:54.271},{x:8,y:36.95},{x:10,y:40.415},{x:23,y:10.96965511},{x:25,y:14.43375673},{x:17.5,y:1.443375673},{x:35,y:-28.86751346},{x:50,y:-28.86751346},{x:-6.5,y:46.47669667},{x:13.5,y:11.83568052},{x:5.5,y:-2.020725942},{x:21,y:-28.86751346},{x:5,y:26.55811238},{x:-27,y:-28.86751346},{x:-50,y:-28.86751346}],i=[{name:"PD",order:[1,2,3]},{name:"T1",order:[3,2,4,5,6]},{name:"T2",order:[6,5,7,8]},{name:"T3",order:[8,9,10,11]},{name:"DT",order:[4,12,13,14,15,10,9,7]},{name:"D1",order:[12,18,17,16]},{name:"D2",order:[16,17,15,14,13]}],o=[];i.forEach((function(t){var e=[];t.order.forEach((function(t){e.push({x:n[t-1].x,y:n[t-1].y})})),o.push({name:t.name,points:e})}));var a={top:0,right:0,bottom:0,left:0},l=e.width-a.left-a.right,s=e.height-a.top-a.bottom,d=this,c=Y.Ys("#"+this.container).append("svg").attr("preserveAspectRatio","xMinYMin meet").attr("viewBox","0 0 438.8 380");this.chart=c.append("g").attr("transform","translate("+a.left+","+a.top+")"),this.xScale=Y.BYU().range([0,l]),this.yScale=Y.BYU().range([s,0]),this.xScale.domain([-50,50]).nice(),this.yScale.domain([-40,60]).nice(),this.chart.selectAll("polygon2").data(o).enter().append("polygon").attr("points",(function(t){return t.points.map((function(t){return[d.xScale(t.x),d.yScale(t.y)].join(",")})).join(" ")})).attr("fill",(function(t){return r[t.name]})).attr("stroke-width",2);var u=Y.Ys("body").append("div").attr("id",this.container+"-tooltip").style("padding","10px").style("font-size","10px").style("background","#fff").style("opacity",0).style("position","absolute").style("visibility","hidden").style("font-family","calibri"),h=this.getChartData(t);this.chart.selectAll("points").data(h).enter().append("circle").attr("r",2.5).attr("cx",(function(t){return d.xScale(t.x)})).attr("cy",(function(t){return d.yScale(t.y)})).attr("id",(function(t){return"plot-"+d.container+"-"+t.key})).style("opacity",.2).style("fill",(function(t){t.color})).on("mouseover",(function(t,e){var r='<table width="100%" class="basic-table" style="font-size:11px;">';r+='<tr><td style="font-weight:bold;">Timestamp </td><td>'+e.date+"</td></tr>",r+='<tr><td style="font-weight:bold;">C2H4 </td><td>'+e.c2h4+"("+e.c2h4_pc.toFixed(2)+"%)</td></tr>",r+='<tr><td style="font-weight:bold;">C2H2 </td><td>'+e.c2h2+"("+e.c2h2_pc.toFixed(2)+"%)</td></tr>",r+='<tr><td style="font-weight:bold;">CH4 </td><td>'+e.ch4+"("+e.ch4_pc.toFixed(2)+"%)</td></tr>",r+='<tr><td style="font-weight:bold;">Fault </td><td>'+e.interpretation+"</td></tr>",r+="</table>",u.html(r).style("left",t.pageX+10+"px").style("top",t.pageY-10+"px"),u.transition().duration(300).style("opacity",1).style("visibility","visible")})).on("mouseout",(function(t){u.transition().duration(300).style("opacity",0).style("visibility","hidden")}));var p=1e3/h.length;h.forEach((function(t,e){var r=Y.Ys("#plot-"+d.container+"-"+e).transition().duration((e+1)*p).style("fill",t.color).style("opacity",1).style("cursor","pointer");e==h.length-1&&r.attr("r",4)}));var f='<table style="font-size:12px; font-family: calibri; text-align:left;">';f+='<tr><td style="width:14px;"><div id="'+this.container+'-legend-0" style="border:1px solid #757575; vertical-align:middle; background : '+r.PD+'; width:10px;height:8px;"></td><td>PD: Coronal Partial Discharge</td></tr>',f+='<tr><td style="width:10px;"><div id="'+this.container+'-legend-1" style="border:1px solid #757575; vertical-align:middle; background : '+r.T1+'; width:10px;height:8px;"></td><td>T1: Thermal Faults, T</td></tr>',f+='<tr><td style="width:10px;"><div id="'+this.container+'-legend-2" style="border:1px solid #757575; vertical-align:middle; background : '+r.T2+'; width:10px;height:8px;"></td><td>T2: Thermal Faults, 300&#8451 < T < 700&#8451</td></tr>',f+='<tr><td style="width:10px;"><div id="'+this.container+'-legend-3" style="border:1px solid #757575; vertical-align:middle; background : '+r.T3+'; width:10px;height:8px;"></td><td>T3: Thermal faults, T > 700&#8451;</td></tr>',f+='<tr><td style="width:10px;"><div id="'+this.container+'-legend-4" style="border:1px solid #757575; vertical-align:middle; background : '+r.DT+'; width:10px;height:8px;"></td><td>DT: Mixture of electrical, thermal faults</td></tr>',f+='<tr><td style="width:10px;"><div id="'+this.container+'-legend-5" style="border:1px solid #757575; vertical-align:middle; background : '+r.D1+'; width:10px;height:8px;"></td><td>D1: Low energy discharges</td></tr>',f+='<tr><td style="width:10px;"><div id="'+this.container+'-legend-6" style="border:1px solid #757575; vertical-align:middle; background : '+r.D2+'; width:10px;height:8px;"></td><td>D2: High energy discharges</td></tr>',f+="</table>";Y.Ys("#"+this.container).append("div").style("padding","0 10px").html(f)}getChartData(t){var e=[],r=this;return t.forEach((function(n,i){var o=n.c2h4,a=n.c2h2,l=n.ch4,s=n.timestamp,d=n.interpretation,c=100,u=Math.sqrt(Math.pow(c,2)-Math.pow(c/2,2)),h=c/2/Math.cos(30/180*Math.PI),p=h-u,f=o+a+l,g=o/f*100,y=a/f*100,m=l/f*100,v=(g-y)*Math.sin(30*Math.PI/180),x=m/100*u+p,b={key:i,x:v,y:x,c2h4:o,c2h2:a,ch4:l,c2h4_pc:g,c2h2_pc:y,ch4_pc:m,date:s,fault:d,color:r.getHexColor(i,t.length)};e.push(b)})),e}getHexColor(t,e){var r=255,n=0,i=255/e,o=255-Math.floor((t+1)*i),a=Number(r).toString(16).length<2?"0"+Number(r).toString(16):Number(r).toString(16),l=Number(o).toString(16).length<2?"0"+Number(o).toString(16):Number(o).toString(16),s=Number(n).toString(16).length<2?"0"+Number(n).toString(16):Number(n).toString(16),d=a+l+s;return"#"+d}updateData(t){var e=this;this.chart.selectAll("circle").remove();var r=this.getChartData(t);this.chart.selectAll("points").data(r).enter().append("circle").attr("r",2.4).attr("cx",(function(t){return e.xScale(t.x)})).attr("cy",(function(t){return e.yScale(t.y)})).attr("id",(function(t){return"plot-"+e.container+"-"+t.key})).style("fill",(function(t){return t.color})).style("cursor","pointer").on("mouseover",(function(t){var e='<table width="100%" class="basic-table" style="font-size:12px;">';e+='<tr><td style="font-weight:bold;">Timestamp </td><td>'+t.date+"</td></tr>",e+='<tr><td style="font-weight:bold;">C2H4 </td><td>'+t.c2h4+"("+t.c2h4_pc.toFixed(2)+"%)</td></tr>",e+='<tr><td style="font-weight:bold;">C2H2 </td><td>'+t.c2h2+"("+t.c2h2_pc.toFixed(2)+"%)</td></tr>",e+='<tr><td style="font-weight:bold;">CH4 </td><td>'+t.ch4+"("+t.ch4_pc.toFixed(2)+"%)</td></tr>",e+='<tr><td style="font-weight:bold;">Fault </td><td>'+t.fault+"</td></tr>",e+="</table>",tooltip.html(e).style("left",Y.event.pageX+10+"px").style("top",Y.event.pageY-10+"px"),tooltip.transition().duration(300).style("opacity",1).style("visibility","visible")})).on("mouseout",(function(t){tooltip.transition().duration(300).style("opacity",0).style("visibility","hidden")})),Y.Ys("#plot-"+div_id+"-"+(r.length-1)).transition().duration(100).attr("r",4)}}var B={name:"DuvalsTriangleOne",data(){return{duvalst1:null,config:{width:"438.8",height:"380"}}},mounted(){this.duvals=new I("triangle1",this.config),this.duvals.load([{timestamp:"01/01/2022 01:00:00",c2h2:12,c2h4:15,ch4:10}])},methods:{}},L=B,z=(0,d.Z)(L,A,E,!1,null,null,null),G=z.exports,q={name:"Home",data(){return{}},methods:{},components:{DuvalsTriangleOne:G}},U=q,W=(0,d.Z)(U,F,N,!1,null,null,null),R=W.exports,V=r(3816),X=r(5255),$=r(1273),Q=function(){var t=this,e=t._self._c;return e(V.Z,{staticClass:"mx-auto mt-15",attrs:{"max-width":"480"}},[e($.Z,{attrs:{src:"https://cdn.vuetifyjs.com/images/cards/sunshine.jpg",height:"200px"}}),e(X.EB,[t._v(" C8 OHMS ")]),e(X.Qq),e(X.ZB,[e(g.Z,{on:{click:function(e){return t.signInWithGoogle()}}},[t._v("Sign-In With Google")])],1)],1)},J=[],K={name:"Login",components:{},created(){},data(){return{}},methods:{signInWithGoogle(){try{console.log(this.$gAuth.signIn())}catch(t){return console.error(t),null}}}},tt=K,et=(0,d.Z)(tt,Q,J,!1,null,null,null),rt=et.exports;n.ZP.use(h.Z);const nt=[{path:"/about",name:"about",component:function(){return r.e(443).then(r.bind(r,4403))}},{path:"/login",name:"Login",component:rt},{path:"/dashboard",name:"Dashboard",component:H,children:[{path:"/home",name:"Home",component:R}]}],it=new h.Z({routes:nt});var ot=it,at=r(629);n.ZP.use(at.ZP);var lt=new at.ZP.Store({state:{},getters:{},mutations:{},actions:{},modules:{}}),st=r(154);n.ZP.use(st.Z);var dt=new st.Z({}),ct=r(8831);const ut={clientId:"993267277168-t4ot4v30dh7cfsrob9c41f07ifcare7m.apps.googleusercontent.com",scope:"profile email",prompt:"select_account"};n.ZP.use(ct.Z,ut),n.ZP.config.productionTip=!1,new n.ZP({router:ot,store:lt,vuetify:dt,render:function(t){return t(u)}}).$mount("#app")}},e={};function r(n){var i=e[n];if(void 0!==i)return i.exports;var o=e[n]={exports:{}};return t[n](o,o.exports,r),o.exports}r.m=t,function(){var t=[];r.O=function(e,n,i,o){if(!n){var a=1/0;for(c=0;c<t.length;c++){n=t[c][0],i=t[c][1],o=t[c][2];for(var l=!0,s=0;s<n.length;s++)(!1&o||a>=o)&&Object.keys(r.O).every((function(t){return r.O[t](n[s])}))?n.splice(s--,1):(l=!1,o<a&&(a=o));if(l){t.splice(c--,1);var d=i();void 0!==d&&(e=d)}}return e}o=o||0;for(var c=t.length;c>0&&t[c-1][2]>o;c--)t[c]=t[c-1];t[c]=[n,i,o]}}(),function(){r.d=function(t,e){for(var n in e)r.o(e,n)&&!r.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})}}(),function(){r.f={},r.e=function(t){return Promise.all(Object.keys(r.f).reduce((function(e,n){return r.f[n](t,e),e}),[]))}}(),function(){r.u=function(t){return"js/about.713fd17f.js"}}(),function(){r.miniCssF=function(t){}}(),function(){r.g=function(){if("object"===typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"===typeof window)return window}}()}(),function(){r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)}}(),function(){var t={},e="dga-ui:";r.l=function(n,i,o,a){if(t[n])t[n].push(i);else{var l,s;if(void 0!==o)for(var d=document.getElementsByTagName("script"),c=0;c<d.length;c++){var u=d[c];if(u.getAttribute("src")==n||u.getAttribute("data-webpack")==e+o){l=u;break}}l||(s=!0,l=document.createElement("script"),l.charset="utf-8",l.timeout=120,r.nc&&l.setAttribute("nonce",r.nc),l.setAttribute("data-webpack",e+o),l.src=n),t[n]=[i];var h=function(e,r){l.onerror=l.onload=null,clearTimeout(p);var i=t[n];if(delete t[n],l.parentNode&&l.parentNode.removeChild(l),i&&i.forEach((function(t){return t(r)})),e)return e(r)},p=setTimeout(h.bind(null,void 0,{type:"timeout",target:l}),12e4);l.onerror=h.bind(null,l.onerror),l.onload=h.bind(null,l.onload),s&&document.head.appendChild(l)}}}(),function(){r.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})}}(),function(){r.p="/"}(),function(){var t={143:0};r.f.j=function(e,n){var i=r.o(t,e)?t[e]:void 0;if(0!==i)if(i)n.push(i[2]);else{var o=new Promise((function(r,n){i=t[e]=[r,n]}));n.push(i[2]=o);var a=r.p+r.u(e),l=new Error,s=function(n){if(r.o(t,e)&&(i=t[e],0!==i&&(t[e]=void 0),i)){var o=n&&("load"===n.type?"missing":n.type),a=n&&n.target&&n.target.src;l.message="Loading chunk "+e+" failed.\n("+o+": "+a+")",l.name="ChunkLoadError",l.type=o,l.request=a,i[1](l)}};r.l(a,s,"chunk-"+e,e)}},r.O.j=function(e){return 0===t[e]};var e=function(e,n){var i,o,a=n[0],l=n[1],s=n[2],d=0;if(a.some((function(e){return 0!==t[e]}))){for(i in l)r.o(l,i)&&(r.m[i]=l[i]);if(s)var c=s(r)}for(e&&e(n);d<a.length;d++)o=a[d],r.o(t,o)&&t[o]&&t[o][0](),t[o]=0;return r.O(c)},n=self["webpackChunkdga_ui"]=self["webpackChunkdga_ui"]||[];n.forEach(e.bind(null,0)),n.push=e.bind(null,n.push.bind(n))}();var n=r.O(void 0,[998],(function(){return r(5971)}));n=r.O(n)})();
//# sourceMappingURL=app.43ea5a1d.js.map