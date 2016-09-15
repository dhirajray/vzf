// JavaScript Document
/*Shadowbox.init({
    // let's skip the automatic setup because we don't have any
    // properly configured link elements on the page
    skipSetup: true
});
*/
Shadowbox.init({
    modal: true
});

function OpenShadowbox(ShadowBoxFileName,ShadowBoxTitle,ShadowBoxHeight,ShadowBoxWidth) {
    Shadowbox.open({
        content:    ShadowBoxFileName,
        player:     "iframe",
        title:      ShadowBoxTitle,
        height:     ShadowBoxHeight,
        width:      ShadowBoxWidth
   });
}

function RefreshShadowbox(ShadowBoxFileName,ShadowBoxTitle,ShadowBoxHeight,ShadowBoxWidth){
	window.parent.Shadowbox.refresh({
		content: ShadowBoxFileName,
		player:  'iframe',
		title:   ShadowBoxTitle,
		height:  ShadowBoxHeight,
		width:   ShadowBoxWidth
	});
};