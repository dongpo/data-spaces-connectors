package tw.geomni.tms;
import java.nio.file.Path;
import java.time.Instant;
import org.eclipse.edc.runtime.metamodel.annotation.Inject;
import org.eclipse.edc.spi.system.*;
import org.eclipse.edc.policy.engine.spi.*;
import org.eclipse.edc.policy.model.*;
import org.eclipse.edc.connector.controlplane.contract.spi.policy.*;

/** Enforces platform contract eligibility during real EDC negotiation and transfer authorization. */
public class TtcPolicyExtension implements ServiceExtension {
 public static final String OPERAND="https://geomni.tw/ttc/approvedContract";
 @Inject private PolicyEngine engine;
 @Inject private RuleBindingRegistry bindings;
 private TtcClient client; private long provider,consumer;private String providerId,consumerId;
 public void initialize(ServiceExtensionContext context){
  var c=context.getConfig();var base=c.getString("ttc.base.url","");
  // No TTC products may be published until configured; normal isolated EDC baseline has no such products.
  if(base.isBlank())return;
  try{client=new TtcClient(base,c.getString("ttc.client.id"),Path.of(c.getString("ttc.private.key.path")),c.getBoolean("ttc.isolated.http",false));}
  catch(Exception e){throw new IllegalStateException("TTC integration configuration is invalid");}
  provider=Long.parseLong(c.getString("ttc.provider.org.id"));consumer=Long.parseLong(c.getString("ttc.consumer.org.id"));
  providerId=c.getString("ttc.provider.participant.id");consumerId=c.getString("ttc.consumer.participant.id");
  // Bind the permission action too: otherwise EDC omits the entire use rule during negotiation.
  bindings.bind("http://www.w3.org/ns/odrl/2/use",ContractNegotiationPolicyContext.NEGOTIATION_SCOPE);
  bindings.bind("http://www.w3.org/ns/odrl/2/use",TransferProcessPolicyContext.TRANSFER_SCOPE);
  bindings.bind(OPERAND,ContractNegotiationPolicyContext.NEGOTIATION_SCOPE);
  bindings.bind(OPERAND,TransferProcessPolicyContext.TRANSFER_SCOPE);
  var own=c.getString("edc.participant.id");
  var expectedPeer=own.equals(providerId)?consumerId:own.equals(consumerId)?providerId:"";
  if(expectedPeer.isBlank())throw new IllegalStateException("Unknown local participant mapping");
  engine.registerFunction(ContractNegotiationPolicyContext.class,Permission.class,OPERAND,(op,value,rule,ctx)->check(op,value,expectedPeer,ctx.participantAgent().getIdentity()));
  engine.registerFunction(TransferProcessPolicyContext.class,Permission.class,OPERAND,(op,value,rule,ctx)->check(op,value,expectedPeer,ctx.participantAgent().getIdentity()));
 }
 private boolean check(Operator op,Object value,String expected,String actual){
  if(op!=Operator.EQ || !expected.equals(actual))return false;
  try{
   var parts=value.toString().split(":");if(parts.length!=3)return false;
   long id=Long.parseLong(parts[0]),product=Long.parseLong(parts[1]),version=Long.parseLong(parts[2]);
   var contract=client.eligibility(id);
   // Unsupported runtime constraints are rejected until per-request quota enforcement is implemented.
   var constraints=contract.path("constraints");if(!constraints.isMissingNode() && constraints.size()!=0)return false;
   return TtcClient.matches(contract,id,product,version,provider,consumer,Instant.now().getEpochSecond());
  }catch(Exception e){return false;}
 }
}
